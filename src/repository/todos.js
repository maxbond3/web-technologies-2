import api from "../services/api.js";

class TodosRepository {
  async getAll() {
    const response = await api("/todo", {
      method: "GET",
    });
    return response;
  }

  async create(description) {
    const response = await api("/todo", {
      method: "POST",
      body: JSON.stringify({ description }),
    });
    return response;
  }

  async update(id, completed) {
    const response = await api(`/todo/${id}`, {
      method: "PUT",
      body: JSON.stringify({ completed }),
    });
    return response;
  }

  async delete(id) {
    const response = await api(`/todo/${id}`, {
      method: "DELETE",
    });
    return response;
  }
}

export default new TodosRepository();
