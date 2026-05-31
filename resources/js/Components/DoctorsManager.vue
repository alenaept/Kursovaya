<template>
  <div class="admin-section">
    <div class="section-header">
      <h2>Управление врачами</h2>
      <button @click="openCreateModal" class="btn-primary">+ Добавить врача</button>
    </div>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>ФИО</th>
            <th>Специализация</th>
            <th>Опыт</th>
            <th>Email</th>
            <th>Телефон</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in doctors" :key="item.id_doctor">
            <td>{{ item.id_doctor }}</td>
            <td>{{ item.last_name }} {{ item.first_name }}</td>
            <td>{{ item.specialization }}</td>
            <td>{{ item.experience_years }} лет</td>
            <td>{{ item.email }}</td>
            <td>{{ item.phone }}</td>
            <td>
              <button @click="openEditModal(item)" class="btn-edit">✏️</button>
              <button @click="handleDelete(item)" class="btn-delete">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Модальное окно -->
    <div v-if="modalVisible" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content modal-large">
        <h3>{{ modalTitle }}</h3>
        <form @submit.prevent="handleSubmit">
          <div class="form-row">
            <div class="form-group">
              <label>Имя *</label>
              <input v-model="formData.first_name" type="text" required>
            </div>
            <div class="form-group">
              <label>Фамилия *</label>
              <input v-model="formData.last_name" type="text" required>
            </div>
          </div>
          <div class="form-group">
            <label>Специализация *</label>
            <input v-model="formData.specialization" type="text" required>
          </div>
          <div class="form-group">
            <label>Биография</label>
            <textarea v-model="formData.bio" rows="3"></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Опыт (лет) *</label>
              <input v-model.number="formData.experience_years" type="number" min="0" required>
            </div>
            <div class="form-group">
              <label>Email *</label>
              <input v-model="formData.email" type="email" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Телефон *</label>
              <input v-model="formData.phone" type="text" required>
            </div>
            <div class="form-group" v-if="!editingId">
              <label>Пароль *</label>
              <input v-model="formData.password" type="password" required>
            </div>
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
  name: 'DoctorsManager',
  data() {
    return {
      doctors: [],
      modalVisible: false,
      editingId: null,
      formData: {
        first_name: '',
        last_name: '',
        specialization: '',
        bio: '',
        experience_years: 0,
        email: '',
        phone: '',
        password: ''
      }
    }
  },
  computed: {
    modalTitle() {
      return this.editingId ? 'Редактировать врача' : 'Добавить врача'
    }
  },
  mounted() {
    this.loadDoctors()
  },
  methods: {
    async loadDoctors() {
      try {
        const response = await fetch('/api/doctors', {
          headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        })
        this.doctors = await response.json()
      } catch (error) {
        console.error('Ошибка загрузки:', error)
      }
    },
    openCreateModal() {
      this.editingId = null
      this.formData = {
        first_name: '', last_name: '', specialization: '',
        bio: '', experience_years: 0, email: '', phone: '', password: ''
      }
      this.modalVisible = true
    },
    openEditModal(item) {
      this.editingId = item.id_doctor
      const { password, ...rest } = item
      this.formData = { ...rest, password: '' }
      this.modalVisible = true
    },
    closeModal() {
      this.modalVisible = false
    },
    async handleSubmit() {
      try {
        const url = this.editingId ? `/api/doctors/${this.editingId}` : '/api/doctors'
        const method = this.editingId ? 'PUT' : 'POST'
        const data = { ...this.formData }
        if (!this.editingId) delete data.password
        
        await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(data)
        })
        
        this.closeModal()
        this.loadDoctors()
      } catch (error) {
        console.error('Ошибка сохранения:', error)
      }
    },
    async handleDelete(item) {
      if (confirm(`Удалить врача ${item.first_name} ${item.last_name}?`)) {
        try {
          await fetch(`/api/doctors/${item.id_doctor}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
          })
          this.loadDoctors()
        } catch (error) {
          console.error('Ошибка удаления:', error)
        }
      }
    }
  }
}
</script>

<style scoped>
/* Добавьте к стилям из предыдущего компонента */
.modal-large {
  width: 700px;
  max-width: 90%;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
/* Остальные стили такие же как в SpecialOffersManager */
</style>