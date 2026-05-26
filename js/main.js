document.addEventListener("DOMContentLoaded", function () {
  initCart();
  animateElements();
  initRatingStars();
  initReviewForm();
});

function initRatingStars() {
  const stars = document.querySelectorAll("#ratingStars .star");
  const ratingInput = document.getElementById("ratingValue");
  const ratingError = document.querySelector(".rating-error");

  if (!stars.length || !ratingInput) return;

  // Установить начальное значение если есть
  const initialValue = parseInt(ratingInput.value) || 0;
  if (initialValue > 0) {
    updateStarsVisual(stars, initialValue);
  }

  stars.forEach((star) => {
    star.addEventListener("click", function () {
      const value = parseInt(this.dataset.value);
      ratingInput.value = value;
      ratingError.style.display = "none";
      updateStarsVisual(stars, value);
    });

    star.addEventListener("mouseenter", function () {
      const value = parseInt(this.dataset.value);
      previewStars(stars, value);
    });

    star.addEventListener("mouseleave", function () {
      const currentValue = parseInt(ratingInput.value) || 0;
      updateStarsVisual(stars, currentValue);
    });
  });
}

function updateStarsVisual(stars, value) {
  stars.forEach((star) => {
    const starValue = parseInt(star.dataset.value);
    if (starValue <= value) {
      star.classList.add("active");
    } else {
      star.classList.remove("active");
    }
  });
}

function previewStars(stars, value) {
  stars.forEach((star) => {
    const starValue = parseInt(star.dataset.value);
    if (starValue <= value) {
      star.classList.add("active");
    } else {
      star.classList.remove("active");
    }
  });
}

function initReviewForm() {
  const form = document.getElementById("reviewForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    const rating = parseInt(document.getElementById("ratingValue").value) || 0;
    const ratingError = document.querySelector(".rating-error");

    if (rating === 0) {
      e.preventDefault();
      ratingError.style.display = "block";
      return false;
    }

    ratingError.style.display = "none";
    return true;
  });
}

function initCart() {
  const cartButtons = document.querySelectorAll(
    ".add-to-cart, .add-to-cart-large",
  );

  cartButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const productId = this.dataset.id;
      addToCart(productId);

      const originalText = this.textContent;
      this.textContent = "Добавлено! ✓";
      this.style.background = "#28a745";

      setTimeout(() => {
        this.textContent = originalText;
        this.style.background = "";
      }, 2000);
    });
  });
}

function addToCart(productId) {
  let cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const existingItem = cart.find((item) => item.id === productId);

  if (existingItem) {
    existingItem.quantity++;
  } else {
    cart.push({ id: productId, quantity: 1 });
  }

  localStorage.setItem("cart", JSON.stringify(cart));
}

function animateElements() {
  const cards = document.querySelectorAll(".product-card");

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1";
        entry.target.style.transform = "translateY(0)";
      }
    });
  });

  cards.forEach((card, index) => {
    card.style.opacity = "0";
    card.style.transform = "translateY(20px)";
    card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
    observer.observe(card);
  });
}
