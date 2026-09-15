(function () {
  "use strict";

  // Mobile navigation toggle
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("main-nav");
  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      var isOpen = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", String(isOpen));
    });
    nav.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        nav.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  // Footer year stays current automatically
  document.querySelectorAll("[data-year]").forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });

  // Contact form: prevent double submission, let the browser handle validation/POST
  var form = document.getElementById("contact-form");
  if (form) {
    form.addEventListener("submit", function () {
      if (!form.checkValidity()) { return; }
      var btn = form.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.textContent = "Wird gesendet …";
      }
    });
  }

  // Show a notice if the form handler redirected back with an error
  var params = new URLSearchParams(window.location.search);
  if (params.get("error")) {
    var alertBox = document.getElementById("form-error");
    if (alertBox) {
      alertBox.hidden = false;
      alertBox.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }
})();
