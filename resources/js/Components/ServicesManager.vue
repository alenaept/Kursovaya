<template>
  <div class="admin-section">
    <div class="section-header">
      <h2>Управление услугами</h2>
      <button @click="openCreateModal" class="btn-primary">+ Добавить услугу</button>
    </div>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Цена</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in services" :key="item.id_services">
            <td>{{ item.id_services }}</td>
            <td><strong>{{ item.name }}</strong></td>
            <td>{{ item.description }}</td>
            <td><span class="price">{{ formatPrice(item.price) }}</span></td>
            <td>
              <button @click="openEditModal(item)" class="btn-edit">✏️</button>
              <button @click="handleDelete(item)" class="btn-delete">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="modalVisible" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <h3>{{ modalTitle }}</h3>
        <form @submit.prevent="handleSubmit">
          <div class="form-group">
            <label>Название услуги *</label>
            <input v-model="formData.name" type="text" required>
          </div>
          <div class="form-group">
            <label>Описание</label>
            <textarea v-model="formData.description" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Цена *</label>
            <input v-model.number="formData.price" type="number" min="0" step="0.01" required>
          </div>
          <div class="modal-buttons">
            <button type="button" @click="closeModal" class="btn-cancel">Отмена</button>
            <button type="submit" class="btn-save">Сохранить</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ServicesManager',
  data() {
    return {
      services: [],
      modalVisible: false,
      editingId: null,
      formData: {
        name: '',
        description: '',
        price: 0
      }
    }
  },
  computed: {
    modalTitle() {
      return this.editingId ? 'Редактировать услугу' : 'Добавить услугу'
    }
  },
  mounted() {
    this.loadServices()
  },
  methods: {
    formatPrice(price) {
      return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(price)
    },
    async loadServices() {
      try {
        const response = await fetch('/api/services', {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        })
        this.services = await response.json()
      } catch (error) {
        console.error('Ошибка загрузки:', error)
      }
    },
    openCreateModal() {
      this.editingId = null
      this.formData = { name: '', description: '', price: 0 }
      this.modalVisible = true
    },
    openEditModal(item) {
      this.editingId = item.id_services
      this.formData = { ...item }
      this.modalVisible = true
    },
    closeModal() {
      this.modalVisible = false
    },
    async handleSubmit() {
      try {
        const url = this.editingId ? `/api/services/${this.editingId}` : '/api/services'
        const method = this.editingId ? 'PUT' : 'POST'
        
        await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(this.formData)
        })
        
        this.closeModal()
        this.loadServices()
      } catch (error) {
        console.error('Ошибка сохранения:', error)
      }
    },
    async handleDelete(item) {
      if (confirm(`Удалить услугу "${item.name}"?`)) {
        try {
          await fetch(`/api/services/${item.id_services}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
          })
          this.loadServices()
        } catch (error) {
          console.error('Ошибка удаления:', error)
        }
      }
    }
  }
}
</script>

<style scoped>
.price {
  color: #059669;
  font-weight: bold;
}
/* Остальные стили из первого компонента */
</style>