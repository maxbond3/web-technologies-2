import Auth from "../services/auth.js";
import location from "../services/location.js";
import loading from "../services/loading.js";
import Form from "../components/form.js";
import TodosService from "../services/todos.js";

let todos = [];

const renderTodos = () => {
  const main = document.querySelector(".main");
  if (!main) return;

  const todosHtml = todos
    .map(
      (todo) => `
        <div class="todo-item" data-id="${todo.id}">
            <div class="todo-content">
                <input type="checkbox" class="todo-checkbox" 
                    ${todo.completed ? "checked" : ""} data-id="${todo.id}" />
                <span class="todo-text ${todo.completed ? "completed" : ""}">${escapeHtml(todo.description)}</span>
            </div>
            <button class="todo-delete" data-id="${todo.id}">Удалить</button>
        </div>
    `,
    )
    .join("");

  main.innerHTML = `
        <div class="todos-container">
            <h2>Мои задачи</h2>
            <form id="todoForm">
                <input type="text" name="description" placeholder="Что нужно сделать?" required />
                <button type="submit">Добавить</button>
            </form>
            <div id="todoFormError" class="form-error"></div>
            <div class="todos-list">${todosHtml || '<div class="empty-state">Нет задач. Добавьте первую!</div>'}</div>
        </div>
    `;

  initTodoEvents();
};

const escapeHtml = (str) => {
  if (!str) return "";
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
};

const loadTodos = async () => {
  loading.start();
  try {
    todos = await TodosService.getAll();
    renderTodos();
  } catch (error) {
    console.error("Ошибка загрузки задач:", error);
    showError("Не удалось загрузить задачи");
  } finally {
    loading.stop();
  }
};

const addTodo = async (description) => {
  if (!description || !description.trim()) {
    showError("Введите описание задачи");
    return;
  }

  loading.start();
  try {
    const newTodo = await TodosService.create(description.trim());
    if (newTodo) {
      todos.unshift(newTodo);
      renderTodos();

      const form = document.querySelector("#todoForm");
      if (form) form.reset();
      hideError();
    }
  } catch (error) {
    console.error("Ошибка добавления задачи:", error);
    showError("Не удалось добавить задачу");
  } finally {
    loading.stop();
  }
};

const toggleTodo = async (id, completed) => {
  loading.start();
  try {
    const updated = await TodosService.update(id, completed);
    if (updated) {
      const index = todos.findIndex((t) => t.id == id);
      if (index !== -1) {
        todos[index] = updated;
        renderTodos();
      }
    }
  } catch (error) {
    console.error("Ошибка обновления задачи:", error);
    showError("Не удалось обновить статус задачи");
    renderTodos();
  } finally {
    loading.stop();
  }
};

const deleteTodo = async (id) => {
  if (!confirm("Вы уверены, что хотите удалить эту задачу?")) return;

  loading.start();
  try {
    await TodosService.delete(id);
    todos = todos.filter((t) => t.id != id);
    renderTodos();
    hideError();
  } catch (error) {
    console.error("Ошибка удаления задачи:", error);
    showError("Не удалось удалить задачу");
  } finally {
    loading.stop();
  }
};

const showError = (message) => {
  const errorEl = document.getElementById("todoFormError");
  if (errorEl) {
    errorEl.textContent = message;
    errorEl.style.display = "block";
    setTimeout(() => {
      if (errorEl) errorEl.style.display = "none";
    }, 3000);
  }
};

const hideError = () => {
  const errorEl = document.getElementById("todoFormError");
  if (errorEl) {
    errorEl.style.display = "none";
    errorEl.textContent = "";
  }
};

const initTodoEvents = () => {
  const form = document.querySelector("#todoForm");
  if (form) {
    new Form(
      form,
      {
        description: (value) => {
          if (!value || !value.trim()) return "Введите задачу";
          if (value.trim().length < 3) return "Минимум 3 символа";
          if (value.trim().length > 200) return "Максимум 200 символов";
          return true;
        },
      },
      (values) => addTodo(values.description),
    );
  }

  document.querySelectorAll(".todo-checkbox").forEach((cb) => {
    cb.removeEventListener("change", handleCheckboxChange);
    cb.addEventListener("change", handleCheckboxChange);
  });

  document.querySelectorAll(".todo-delete").forEach((btn) => {
    btn.removeEventListener("click", handleDeleteClick);
    btn.addEventListener("click", handleDeleteClick);
  });
};

const handleCheckboxChange = (e) => {
  e.stopPropagation();
  const id = e.target.dataset.id;
  const completed = e.target.checked;
  toggleTodo(id, completed);
};

const handleDeleteClick = (e) => {
  e.stopPropagation();
  const id = e.target.dataset.id;
  deleteTodo(id);
};

const init = async () => {
  try {
    const response = await Auth.me();

    if (!response || !response.data) {
      return location.login();
    }

    loading.stop();
    await loadTodos();
  } catch (error) {
    console.error("Ошибка инициализации:", error);
    location.login();
  }
};

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", init);
} else {
  init();
}
