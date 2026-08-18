document.addEventListener("DOMContentLoaded", () => {
  const menuToggle = document.getElementById("mypage-menu-toggle");
  const mainNav = document.getElementById("main-nav");

  if (!menuToggle || !mainNav) {
    console.warn("メニュー要素が見つかりません。");
    return;
  }

  // メニュー開閉
  menuToggle.addEventListener("click", () => {
    menuToggle.classList.toggle("is-active");
    mainNav.classList.toggle("is-active");

    const isOpen = menuToggle.classList.contains("is-active");

    menuToggle.setAttribute(
      "aria-expanded",
      isOpen ? "true" : "false"
    );
  });

  // メニュー内のリンクをクリックしたら閉じる
  mainNav.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      menuToggle.classList.remove("is-active");
      mainNav.classList.remove("is-active");
      menuToggle.setAttribute("aria-expanded", "false");
    });
  });
});


/**
 * Hakuhousha Framework
 */
window.HKS = window.HKS || {};

HKS.api = {

    async post(url, data) {

        const response = await fetch(url, {
            method: 'POST',
            body: data
        });

        const json = await response.json();

        return json;
    }

};