document.addEventListener("DOMContentLoaded", function () {
    const noteTextArea = document.querySelector(".add-note textarea");
    const addNoteBtn = document.getElementById("add-note-btn");
    const errorMessage = document.getElementById("error-message");
    const successMessage = document.getElementById("success-message");
    const notesContainer = document.getElementById("notes-container");
    const characterCount = document.createElement("div"); // For showing remaining characters
    const maxCharacters = 1500;

    // Getting the topic ID from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const topicId = urlParams.get('topic_id');

    // Adding the character count box
    characterCount.className = "char-counter";
    characterCount.style.marginTop = "5px";
    characterCount.style.fontSize = "14px";
    characterCount.style.color = "black";

    // Insert character count before the add note button
    addNoteBtn.parentElement.insertBefore(characterCount, addNoteBtn);

    // Function to update the character count dynamically
    noteTextArea.addEventListener("input", () => {
        const currentLength = noteTextArea.value.length;
        let remainingChars = maxCharacters - currentLength;

        // Update the remaining characters text
        characterCount.textContent = `${remainingChars} characters remaining.`;

        // Change character count color based on limit
        if (remainingChars < 50) {
            characterCount.style.color = "red";
        } else {
            characterCount.style.color = "black";
        }
    });

    // Trigger input event to initialize the character count
    noteTextArea.dispatchEvent(new Event('input'));

    // Add event listener to the Add Note button
    addNoteBtn.addEventListener("click", function () {
        // Clear previous messages
        errorMessage.style.display = "none";
        successMessage.style.display = "none";

        // Validate content
        const noteContent = noteTextArea.value.trim();
        if (noteContent === "") {
            errorMessage.textContent = "Note content is required.";
            errorMessage.style.display = "block";
            return;
        }

        if (noteContent.length > maxCharacters) {
            errorMessage.textContent = `Note content must be ${maxCharacters} characters or fewer.`;
            errorMessage.style.display = "block";
            return;
        }

        // Create form data
        const formData = new FormData();
        formData.append('topic_id', topicId);
        formData.append('note_content', noteContent);

        // Create and send AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'save_note.php', true);

        xhr.onload = function () {
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);

                    if (response.error) {
                        errorMessage.textContent = response.error;
                        errorMessage.style.display = "block";
                        return;
                    }

                    if (response.success) {
                        // Show success message
                        successMessage.textContent = "Note added successfully!";
                        successMessage.style.display = "block";

                        // Reset form
                        noteTextArea.value = "";
                        noteTextArea.dispatchEvent(new Event('input')); // Update character count

                        // Add new notes to the page
                        if (response.notes && response.notes.length > 0) {
                            const noNotesMessage = document.getElementById("no-notes-message");
                            if (noNotesMessage) {
                                noNotesMessage.remove();
                            }

                            // Add each new note to the notes container
                            response.notes.forEach(function (note) {
                                const noteHtml = createNoteHtml(note);
                                notesContainer.appendChild(noteHtml);
                            });
                        }
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    errorMessage.textContent = "An error occurred. Please try again.";
                    errorMessage.style.display = "block";
                }
            } else {
                errorMessage.textContent = "An error occurred. Please try again.";
                errorMessage.style.display = "block";
            }
        };

        xhr.onerror = function () {
            errorMessage.textContent = "Connection error. Please try again.";
            errorMessage.style.display = "block";
        };

        xhr.send(formData);
    });

    // Function to create note HTML element
    function createNoteHtml(note) {
        const noteDiv = document.createElement('div');
        noteDiv.className = 'note';

        noteDiv.innerHTML = `
            <div class="note-header">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="${note.avatar}" alt="User Avatar" />
                    </div>
                    <span>${note.screen_name}</span>
                </div>
                <p><strong>Posted:</strong> ${note.created_at_formatted}</p>
            </div>
            <p class="note-text">${note.content.replace(/\n/g, '<br>')}</p>
        `;

        return noteDiv;
    }
});