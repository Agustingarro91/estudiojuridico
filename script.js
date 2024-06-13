/* ********** Menu ********** */
((d) => {
  const $btnMenu = d.querySelector(".menu-btn"),
    $menu = d.querySelector(".menu");

  $btnMenu.addEventListener("click", (e) => {
    $btnMenu.firstElementChild.classList.toggle("none");
    $btnMenu.lastElementChild.classList.toggle("none");
    $menu.classList.toggle("is-active");
  });

  d.addEventListener("click", (e) => {
    if (!e.target.matches(".menu a")) return false;

    $btnMenu.firstElementChild.classList.remove("none");
    $btnMenu.lastElementChild.classList.add("none");
    $menu.classList.remove("is-active");
  });
})(document);

/* ********** ContactForm ********** */
((d) => {
  const $form = d.querySelector(".contact-form"),
    $loader = d.querySelector(".contact-form-loader"),
    $response = d.querySelector(".contact-form-response"),
    $inputs = d.querySelectorAll(".contact-form [required]"),
    $captcha = d.querySelectorAll(".g-recaptcha");

    console.log($captcha);



    $inputs.forEach(input => {
      const $span = d.createElement("span");
      $span.id = input.name;
      $span.textContent = input.title;
      $span.classList.add("contact-form-error", "none"); 
      input.insertAdjacentElement("afterend", $span);
    })
    
    d.addEventListener("keyup", (e) => {
    if(e.target.matches(".contact-form [required]")){
      let $input = e.target,
      pattern = $input.pattern || $input.dataset.pattern;

      if(pattern){
        let regex = new RegExp(pattern);
        return !regex.exec($input.value)
        ? d.getElementById($input.name).classList.add("is-active")
        :d.getElementById($input.name).classList.remove("is-active")
      }
  
      if(!pattern){
        return $input.value === ""
        ? d.getElementById($input.name).classList.add("is-active")
        :d.getElementById($input.name).classList.remove("is-active")
      }
    }


  })

  $form.addEventListener("submit", (e) => {
    e.preventDefault();
    $loader.classList.remove("none");
    fetch("https://agustindiazgarro.top/antunez2/send_mail.php", {
      method: "POST",
      body: new FormData(e.target),
      mode:"cors"
    })
      .then((res) => (res.ok ? res.json() : Promise.reject(res)))
      .then((json) => {
        location.hash = "#gracias";
        $form.reset();
      })
      .catch((err) => {
        let message =
          err.statusText || "Ocurrió un error al enviar, intenta nuevamente";
        $response.querySelector(
          "h3"
        ).innerHTML = `Error ${err.status}: ${message}`;
      })
      .finally(() => {
        $loader.classList.add("none");
        setTimeout(() => {
          location.hash = "#close";
        }, 5000);
      });
  });
})(document);
