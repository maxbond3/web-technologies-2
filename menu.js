document.addEventListener("DOMContentLoaded", function () {
  // Инициализация меню
  initMenu();
});

function initMenu() {
  const menu = document.querySelector(".main-nav");

  if (!menu) {
    console.error("Меню не найдено");
    return;
  }

  // Находим все элементы с подменю
  const menuToggles = menu.querySelectorAll(".menu-toggle");

  menuToggles.forEach(function (toggle) {
    toggle.addEventListener("click", function (e) {
      e.preventDefault();

      const parentLi = this.parentElement;
      const subMenu = parentLi.querySelector(".sub-menu");

      if (!subMenu) return;

      // Переключаем класс open
      if (parentLi.classList.contains("open")) {
        // Закрываем подменю
        parentLi.classList.remove("open");
        // Также закрываем все вложенные подменю
        closeAllSubMenus(parentLi);
      } else {
        // Открываем подменю
        parentLi.classList.add("open");
      }
    });
  });

  // Обработка кликов по обычным ссылкам (без подменю)
  const regularLinks = menu.querySelectorAll(".menu-link:not(.menu-toggle)");
  regularLinks.forEach(function (link) {
    link.addEventListener("click", function (e) {
      // Убираем активный класс у всех элементов
      menu.querySelectorAll(".menu-link").forEach(function (item) {
        item.classList.remove("active");
      });

      // Добавляем активный класс текущему элементу
      this.classList.add("active");
    });
  });

  // Восстановление состояния меню из localStorage
  restoreMenuState(menu);

  // Сохранение состояния при закрытии страницы
  window.addEventListener("beforeunload", function () {
    saveMenuState(menu);
  });
}

/**
 * Закрытие всех вложенных подменю
 */
function closeAllSubMenus(element) {
  const openItems = element.querySelectorAll(".has-submenu.open");
  openItems.forEach(function (item) {
    item.classList.remove("open");
  });
}

/**
 * Сохранение состояния меню в localStorage
 */
function saveMenuState(menu) {
  const openItems = [];
  const openElements = menu.querySelectorAll(".has-submenu.open");

  openElements.forEach(function (element) {
    // Сохраняем путь к элементу
    const path = getElementPath(element);
    openItems.push(path);
  });

  localStorage.setItem("menuState", JSON.stringify(openItems));
}

/**
 * Получение пути к элементу
 */
function getElementPath(element) {
  const path = [];
  let current = element;

  while (current && current !== document.querySelector(".main-nav")) {
    const parent = current.parentElement;
    if (parent) {
      const children = Array.from(parent.children);
      const index = children.indexOf(current);
      path.unshift(index);
    }
    current = current.parentElement;
  }

  return path.join("-");
}

/**
 * Восстановление состояния меню
 */
function restoreMenuState(menu) {
  const savedState = localStorage.getItem("menuState");

  if (!savedState) return;

  try {
    const openItems = JSON.parse(savedState);

    openItems.forEach(function (path) {
      restoreElementByPath(menu, path);
    });
  } catch (e) {
    console.error("Ошибка восстановления состояния меню:", e);
  }
}

/**
 * Восстановление элемента по пути
 */
function restoreElementByPath(menu, path) {
  const indices = path.split("-").map(Number);
  let current = menu;

  for (let i = 0; i < indices.length; i++) {
    const children = Array.from(current.children);
    if (indices[i] < children.length) {
      current = children[indices[i]];
    } else {
      return;
    }
  }

  if (current && current.classList.contains("has-submenu")) {
    current.classList.add("open");
  }
}

// Дополнительная функциональность
document.addEventListener("keydown", function (e) {
  // Закрытие всех меню при нажатии Escape
  if (e.key === "Escape") {
    const menu = document.querySelector(".main-nav");
    if (menu) {
      const openItems = menu.querySelectorAll(".has-submenu.open");
      openItems.forEach(function (item) {
        item.classList.remove("open");
      });
      localStorage.removeItem("menuState");
    }
  }
});
