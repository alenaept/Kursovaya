<template>
  <div class="admin-section">
    <div class="section-header">
      <h2>Заявки от клиентов</h2>
    </div>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Телефон</th>
            <th>Дата</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in requests" :key="item.id_requests">
            <td>{{ item.id_requests }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.phone }}</td>
            <td>{{ formatDate(item.created_at) }}</td>
            <td>
              <button @click="handleDelete(item)" class="btn-delete">🗑️ Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RequestsManager',
  data() {
    return {
      requests: []
    }
  },
  mounted() {
    this.loadRequests()
  },
  methods: {
    formatDate(date) {
      if (!date) return '-'
      return new Date(date).toLocaleString('ru-RU')
    },
    async loadRequests() {
      try {
        const response = await fetch('/api/requests', {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        })
        this.requests = await response.json()
      } catch (error) {
        console.error('Ошибка загрузки:', error)
      }
    },
    async handleDelete(item) {
      if (confirm(`Удалить заявку от ${item.name}?`)) {
        try {
          await fetch(`/api/requests/${item.id_requests}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
          })
          this.loadRequests()
        } catch (error) {
          console.error('Ошибка удаления:', error)
        }
      }
    }
  }
}
</script>

<style scoped>
/* Стили из первого компонента */
</style>