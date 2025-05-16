document.addEventListener('DOMContentLoaded', function () {
    // Store the most recent topic ID for checking new topics
    let lastTopicId = 0;

    // Get all topic containers and find the highest ID
    const topicContainers = document.querySelectorAll('.topic-container');
    topicContainers.forEach(function (container) {
        const topicId = parseInt(container.getAttribute('data-topic-id'));
        if (topicId > lastTopicId) {
            lastTopicId = topicId;
        }
    });

    // Function to check for new topics
    function checkForNewTopics() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `check_new_topics.php?last_topic_id=${lastTopicId}`, true);

        xhr.onload = function () {
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);

                    if (response.error) {
                        console.error('Error checking for new topics:', response.error);
                        return;
                    }

                    if (response.topics && response.topics.length > 0) {
                        // Update the lastTopicId
                        response.topics.forEach(function (topic) {
                            if (topic.topic_id > lastTopicId) {
                                lastTopicId = parseInt(topic.topic_id);
                            }

                            // Create and insert the new topic at the top of the list
                            const topicHtml = createTopicHtml(topic);
                            const topicsContainer = document.getElementById('topics-container');

                            const noTopicsMsg = topicsContainer.querySelector('p');
                            if (noTopicsMsg && noTopicsMsg.textContent.includes('No topics found')) {
                                noTopicsMsg.remove();
                            }

                            // Insert at the beginning
                            if (topicsContainer.firstChild) {
                                topicsContainer.insertBefore(topicHtml, topicsContainer.firstChild);
                            } else {
                                topicsContainer.appendChild(topicHtml);
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
            }
        };

        xhr.onerror = function () {
            console.error('Request error');
        };

        xhr.send();
    }

    // Function to create topic HTML element
    function createTopicHtml(topic) {
        const topicContainer = document.createElement('div');
        topicContainer.className = 'topic-container';
        topicContainer.setAttribute('data-topic-id', topic.topic_id);

        topicContainer.innerHTML = `
            <div class="topic-details">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="${topic.creator_avatar}" alt="Avatar" />
                    </div>
                    <span>${topic.creator_name}</span>
                </div>
                <h3>${topic.title}</h3>
                <p><strong>Posted:</strong> ${topic.created_at_formatted} | 
                   <strong>Last Updated:</strong> ${topic.last_updated_formatted}</p>
                <p><strong>Notes:</strong> ${topic.notes_count}</p>
            </div>
            <div class="topic-actions">
                <button class="view-btn" onclick="window.location.href='view_notes.php?topic_id=${topic.topic_id}';">View Notes</button>
                <button class="access-btn" onclick="window.location.href='grant_access.php?topic_id=${topic.topic_id}';">Access</button>
            </div>
        `;

        return topicContainer;
    }

    // Set interval to check for new topics every 90 seconds
    setInterval(checkForNewTopics, 90000);
});