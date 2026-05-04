import TodosRepository from "../repository/todos.js";

class TodosService {
  async getAll() {
    const response = await TodosRepository.getAll();
    return response.data || [];
  }

  async create(description) {
    const response = await TodosRepository.create(description);
    return response.data;
  }

  async update(id, completed) {
    const response = await TodosRepository.update(id, completed);
    return response.data;
  }

  async delete(id) {
    const response = await TodosRepository.delete(id);
    return response.data;
  }
}

export default new TodosService();
