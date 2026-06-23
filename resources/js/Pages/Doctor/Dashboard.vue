<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
    doctor: Object
});

const appointments = ref([]);
const loading = ref(false);
const selectedDate = ref(new Date().toISOString().split('T')[0]);


const loadAppointments = async () => {
    loading.value = true;
    try {
        const doctorId = props.doctor?.id_doctor || 2;
        const response = await fetch(`/api/doctor/appointments/${doctorId}?date=${selectedDate.value}`);
        const data = await response.json();
        appointments.value = data;
    } catch (err) {
        console.error('Ошибка загрузки записей:', err);
    } finally {
        loading.value = false;
    }
};
const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('ru-RU');
};

const formatTime = (time) => {
    if (!time) return '';
    return time.substring(0, 5);
};


</script>

<template>
    <Header />
    <AuthenticatedLayout>
        <div class="all">
            <div>
                <h1>Расписание</h1>
                <div class="filter-section">
                    <label class="filter-label">Выберите дату:</label>
                    <input 
                        type="date" 
                        v-model="selectedDate" 
                        @change="loadAppointments"
                        class="date-input"
                    >
                </div>

                <div v-if="loading" class="loading">
                    <p>Загрузка записей...</p>
                </div>

                <div v-else-if="appointments.length === 0" class="empty-state">
                    <p>Нет записей на {{ formatDate(selectedDate) }}</p>
                </div>

                <div v-else class="appointments-list">
    <div 
        v-for="appointment in appointments" 
        :key="appointment.id"
        class="appointment-card"
    >
        <div class="appointment-header">
            <div class="patient-info">
                <span class="patient-name">{{ appointment.patient_name }}</span>
                <span class="patient-gender">{{ appointment.patient_gender }}</span>
                <span class="patient-birth">({{ appointment.patient_birth_date }})</span>
            </div>
            <div class="appointment-time">
                {{ formatTime(appointment.time) }}
            </div>
        </div>
        
        <div class="appointment-details">
            <div class="detail-item">
                <span class="detail-label">Телефон:</span>
                <span class="detail-value">{{ appointment.patient_phone || 'Не указан' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Услуга:</span>
                <span class="detail-value">{{ appointment.service }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Цена:</span>
                <span class="detail-value">{{ appointment.price.toLocaleString() }} ₽</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Причина:</span>
                <span class="detail-value">{{ appointment.reason || 'Не указана' }}</span>
            </div>
        </div>
    </div>
</div>
                
            </div>
        </div>
    </AuthenticatedLayout>
        <Footer />
</template>

<style scoped>

.all{
    display: flex;
    justify-content: center;
}
h1{
    text-align: center;
    margin-top: 20px;
    font-size: 25px;
    font-weight: bold;
}
.filter-section {
    margin-top: 40px;
    margin-bottom: 50px;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    width: 1500px;
}



.date-input {
    border: 2px solid #265B87;
    border-radius: 8px;
}

.appointments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.appointment-card {
    background: #EEF7FF;
    border-radius: 15px;
    padding: 25px;
    border: 3px solid #A4C3DD;
    width: 1500px;
}

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.patient-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}


.patient-name {
    font-weight: 600;
    font-size: 20px;
}

.appointment-time {
    background: #2c2687;
    padding: 0.25rem 0.75rem;
    border-radius: 10px;
    font-size: 20px;
    color: white;
    border: solid #003cff;
}

.appointment-details {
    margin-bottom: 20px;
}

.appointment-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-status {
    padding: 0.25rem 0.75rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.2s;
}

.btn-status.confirm {
    background: #5f56e0;
    color: #0400ff;
}

.btn-status.confirm:hover {
    background: #0051ff;
}

.btn-status.complete {
    background: #dbeafe;
    color: #1e40af;
}

.btn-status.complete:hover {
    background: #bfdbfe;
}

.btn-status.cancel {
    background: #fee2e2;
    color: #991b1b;
}

.btn-status.cancel:hover {
    background: #fecaca;
}

.loading {
    text-align: center;
    padding: 3rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #64748b;
    background: white;
    border-radius: 12px;
}
</style> 