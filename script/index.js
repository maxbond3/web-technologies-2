import { PIZZA_TYPES, SIZES, TOPPINGS } from "./data.js";
import { Pizza } from "./pizza.js";

let currentPizza = null;

// Выбор вида пиццы
document.querySelectorAll("#pizzaTypes .option-card").forEach((card) => {
  card.addEventListener("click", () => {
    document
      .querySelectorAll("#pizzaTypes .option-card")
      .forEach((c) => c.classList.remove("selected"));
    card.classList.add("selected");

    const type = card.dataset.type;
    const price = parseInt(card.dataset.price);
    const cal = parseInt(card.dataset.cal);

    // Создаём новую пиццу или обновляем тип
    if (!currentPizza) {
      currentPizza = new Pizza(type, price, cal);
    } else {
      currentPizza.type = type;
      currentPizza.basePrice = price;
      currentPizza.baseCalories = cal;
    }

    updateUI();
  });
});

// Выбор размера
document.querySelectorAll("#pizzaSizes .option-card").forEach((card) => {
  card.addEventListener("click", () => {
    document
      .querySelectorAll("#pizzaSizes .option-card")
      .forEach((c) => c.classList.remove("selected"));
    card.classList.add("selected");

    const sizeKey = card.dataset.size;
    const sizeData = SIZES[sizeKey];

    if (currentPizza) {
      currentPizza.setSize(sizeData);
      updateUI();
    }
  });
});

// Выбор добавок
document.querySelectorAll(".topping-card").forEach((card) => {
  card.addEventListener("click", () => {
    if (!currentPizza) {
      alert("Сначала выберите вид пиццы!");
      return;
    }

    const toppingKey = card.dataset.topping;
    const toppingData = TOPPINGS[toppingKey];

    if (card.classList.contains("selected")) {
      // Убираем добавку
      card.classList.remove("selected");
      currentPizza.removeTopping(toppingData);
    } else {
      // Добавляем добавку
      card.classList.add("selected");
      currentPizza.addTopping(toppingData);
    }

    updateUI();
  });
});

function updateUI() {
  if (!currentPizza) {
    document.getElementById("summaryType").textContent = "—";
    document.getElementById("pizzaImage").src = "";
    document.getElementById("summarySize").textContent = "—";
    document.getElementById("summaryToppings").innerHTML =
      '<span class="no-toppings">нет добавок</span>';
    document.getElementById("summaryPrice").textContent = "0 ₽";
    document.getElementById("summaryCalories").textContent = "0 ккал";
    document.getElementById("pizzaName").textContent = "Выберите пиццу";
    return;
  }

  // Обновляем название
  const typeName = PIZZA_TYPES[currentPizza.type]?.name || currentPizza.type;
  const pizzaImage =
    PIZZA_TYPES[currentPizza.type]?.image || currentPizza.type.image;
  const sizeName = currentPizza.size
    ? SIZES[currentPizza.size.name]?.displayName
    : "";
  document.getElementById("pizzaName").textContent = sizeName
    ? `${typeName} (${sizeName})`
    : typeName;

  // Обновляем итоги
  document.getElementById("summaryType").textContent = typeName;

  document.getElementById("pizzaImage").src = pizzaImage;

  document.getElementById("summarySize").textContent = currentPizza.size
    ? SIZES[currentPizza.size.name]?.displayName
    : "—";

  // Обновляем список добавок
  const toppings = currentPizza.getToppings();
  const toppingsContainer = document.getElementById("summaryToppings");
  if (toppings.length === 0) {
    toppingsContainer.innerHTML =
      '<span class="no-toppings">нет добавок</span>';
  } else {
    toppingsContainer.innerHTML = toppings
      .map((t) => `<span class="topping-tag">${t.name}</span>`)
      .join("");
  }

  // Обновляем цену и калории
  document.getElementById("summaryPrice").textContent =
    currentPizza.calculatePrice() + " ₽";
  document.getElementById("summaryCalories").textContent =
    currentPizza.calculateCalories() + " ккал";

  // Обновляем динамические цены добавок
  updateToppingPrices();
}

function updateToppingPrices() {
  const size = currentPizza?.size?.name;

  // Сырный борт
  const cheeseCrustInfo = document.getElementById("cheese-crust-info");
  const cheeseCrustDynamic = document.getElementById("cheese-crust-dynamic");
  if (size === "large") {
    cheeseCrustInfo.textContent = "+300 ₽ | +50 ккал";
    cheeseCrustDynamic.textContent = "(цена для большой пиццы)";
  } else {
    cheeseCrustInfo.textContent = "+150 ₽ | +50 ккал";
    cheeseCrustDynamic.textContent = size ? "(цена для маленькой пиццы)" : "";
  }

  // Чеддер и пармезан
  const cheddarInfo = document.getElementById("cheddar-info");
  const cheddarDynamic = document.getElementById("cheddar-dynamic");
  if (size === "large") {
    cheddarInfo.textContent = "+300 ₽ | +50 ккал";
    cheddarDynamic.textContent = "(цена для большой пиццы)";
  } else {
    cheddarInfo.textContent = "+150 ₽ | +50 ккал";
    cheddarDynamic.textContent = size ? "(цена для маленькой пиццы)" : "";
  }
}
