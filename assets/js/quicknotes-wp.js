jQuery(document).ready(function ($) {
  function loadQuickNotes() {
    $.ajax({
      url: quicknotes_ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "load_quicknotes",
      },
      success: function (response) {
        if (response.success) {
          let notesList = $(".quicknotes-list");
          notesList.empty();

          response.data.forEach((note) => {
            notesList.append(`
                            <div class="quicknotes-note">
                                <strong>${note.title}</strong>
                                <p>${note.content}</p>
                                <span class="quicknotes-note-date">${note.created_at}</span>
                            </div>
                        `);
          });
        } else {
          alert(response.data);
        }
      },
      error: function () {
        alert("Error al cargar las notas.");
      },
    });
  }

  loadQuickNotes();

  $(".quicknotes-float-btn").on("click", function () {
    loadQuickNotes();
  });

  $(".quicknotes-add-note").on("click", function () {
    $(".quicknotes-add-note-form").toggle();
  });

  $(".quicknotes-save-note").on("click", function () {
    let title = $(".quicknotes-note-title").val();
    let content = $(".quicknotes-note-content").val();

    if (!title || !content) {
      alert("Por favor, complete todos los campos.");
      return;
    }

    $.ajax({
      url: quicknotes_ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "add_quicknote",
        title: title,
        content: content,
      },
      success: function (response) {
        if (response.success) {
          loadQuickNotes();
          $(".quicknotes-add-note-form").hide();
          $(".quicknotes-note-title").val("");
          $(".quicknotes-note-content").val("");
        } else {
          alert(response.data);
        }
      },
      error: function () {
        alert("Error al agregar la nota.");
      },
    });
  });

  var isClosed = false;

  // Manejar el evento de clic en el elemento .quicknotes-float-btn
  $(".quicknotes-float-btn").on("click", function (event) {
    // Evitar que se propague el evento de clic
    event.stopPropagation();

    if (!isClosed) {
      // Mostrar u ocultar el modal
      $(".quicknotes-modal").toggleClass("quicknotes-modal-visible");
    } else {
      $(".quicknotes-modal").removeClass("quicknotes-modal-visible");
    }

    isClosed = !isClosed;
  });

  // Manejar el límite de caracteres en el editor de texto enriquecido
  $(".quicknotes-note-content").on("input", function () {
    var maxLength = $(this).data("maxlength");
    var currentLength = $(this).text().length;

    if (currentLength > maxLength) {
      $(this).text($(this).text().substring(0, maxLength));
      placeCaretAtEnd(this);
    }
  });

  // Función para colocar el cursor al final del contenido
  function placeCaretAtEnd(el) {
    el.focus();
    var range = document.createRange();
    var sel = window.getSelection();
    range.selectNodeContents(el);
    range.collapse(false);
    sel.removeAllRanges();
    sel.addRange(range);
  }

  // Inicializa el editor de texto enriquecido
//   $(".quicknotes-note-content").richText({
//     maxLength: 1000, // Límite de caracteres
//     // uploads
//     imageUpload: false,
//     fileUpload: false,

//     // media
//     videoEmbed: false,

//     // fonts
//     fonts: false,
//     fontColor: false,
//     backgroundColor: false,
//     fontSize: false,

//     // tables
//     table: false,

//     // code
//     removeStyles: false,
//     code: false,
//     // Aquí puedes agregar más opciones de configuración si es necesario
//   });
});
