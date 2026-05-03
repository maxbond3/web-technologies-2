export class Pizza {
  constructor(type, basePrice, baseCalories) {
    this.type = type;
    this.basePrice = basePrice;
    this.baseCalories = baseCalories;
    this.size = null;
    this.toppings = new Set();
  }

  setSize(size) {
    this.size = size;
  }

  getSize() {
    return this.size;
  }

  getStuffing() {
    return this.type;
  }

  addTopping(topping) {
    this.toppings.add(topping);
  }

  removeTopping(topping) {
    this.toppings.delete(topping);
  }

  getToppings() {
    return [...this.toppings];
  }

  calculatePrice() {
    let total = this.basePrice;

    if (this.size) {
      total += this.size.price;
    }

    this.toppings.forEach((topping) => {
      if (topping.priceSmall && topping.priceLarge) {
        if (this.size?.name === "large") {
          total += topping.priceLarge;
        } else {
          total += topping.priceSmall;
        }
      } else {
        total += topping.price;
      }
    });

    return total;
  }

  calculateCalories() {
    let total = this.baseCalories;

    if (this.size) {
      total += this.size.calories;
    }

    this.toppings.forEach((topping) => {
      total += topping.calories;
    });

    return total;
  }
}
