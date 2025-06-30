function setupListeners() {
  const form = document.getElementById("form");
  const input = document.getElementById("message");
  const chatBox = document.querySelector("main");
function loadMessages() {
  fetch("../scripts/get_messages.php")
    .then(response => response.json())
    .then(messages => {
      chatBox.innerHTML = ""; // clear messages

      messages.forEach(message => {
        const section = document.createElement("section");

        // Définir les classes selon l'émetteur et son rôle
        if (message.emetteur_id === currentUserId) {
          section.classList.add('sent');
        } else {
          section.classList.add('received');
        }

        // Ajouter la classe du rôle (parrain ou filleul)
        if (message.emetteur_role) {
          section.classList.add(message.emetteur_role);
        }

        section.innerHTML = `
          <p>${message.contenu}</p>
          <time>${new Date(message.date).toLocaleString("fr-FR")}</time>
        `;

        chatBox.appendChild(section);
      });

      chatBox.scrollTop = chatBox.scrollHeight; // scroll to bottom on refresh
    });
}



  form.onsubmit = event => {
    event.preventDefault();
    const text = input.value.trim();
    if (text === "") return;

    const data = new FormData();
    data.append("contenu", text);

    fetch("../scripts/send_message.php", {
      method: "POST",
      body: data
    }).then(() => {
      input.value = "";
      loadMessages();
    });
  };

  loadMessages();
  setInterval(loadMessages, 2000); // refresh every 2 seconds
}

window.addEventListener('load', setupListeners);
