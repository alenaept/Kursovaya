<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    user: Object,
    success: String,
    reviews: Array,
    appointments: Array
});


const reviewForm = useForm({
    review_text: '',
});

const submitReview = () => {
    reviewForm.post(route('user.reviews.store'), {
        onSuccess: () => {
            reviewForm.reset();
        },
    });
};


const doctors = ref([]);
const availableSlots = ref([]);
const selectedDoctorId = ref('');
const selectedDate = ref('');
const selectedSlotId = ref(null);
const selectedSlotTime = ref('');
const bookingReason = ref('');
const loadingDoctors = ref(false);
const loadingSlots = ref(false);
const bookingSuccess = ref(false);
const bookingError = ref('');
const lastCreatedAppointment = ref(null);

const prices = ref([]);
const selectedPriceId = ref('');
const loadingPrices = ref(false);


const loadPrices = async () => {
    loadingPrices.value = true;
    try {
        const response = await fetch('/api/public/prices');
        prices.value = await response.json();
        console.log('Загружены цены:', prices.value);
    } catch (err) {
        console.error('Ошибка загрузки цен:', err);
    } finally {
        loadingPrices.value = false;
    }
};


const loadDoctors = async () => {
    loadingDoctors.value = true;
    try {
        const response = await fetch('/api/public/doctors');
        doctors.value = await response.json();
        console.log('Загружены врачи:', doctors.value);
    } catch (err) {
        console.error('Ошибка загрузки врачей:', err);
    } finally {
        loadingDoctors.value = false;
    }
};


const loadAvailableSlots = async () => {
    if (!selectedDoctorId.value || !selectedDate.value) {
        availableSlots.value = [];
        return;
    }
    
    loadingSlots.value = true;
    bookingError.value = '';
    
    try {
        console.log('Запрос слотов:', `/api/slots/${selectedDoctorId.value}?date=${selectedDate.value}`);
        
        const response = await fetch(`/api/slots/${selectedDoctorId.value}?date=${selectedDate.value}`);
        const data = await response.json();
        
        console.log('Данные от API:', data);
        console.log('Тип данных:', typeof data);
        console.log('Длина массива:', data.length);
        
        if (data.length === 0) {
            console.log('Нет слотов на выбранную дату');
            availableSlots.value = [];
        } else {
            availableSlots.value = data.map(slot => ({
                id: slot.id,
                start_time: slot.start_time.substring(0, 5), 
                end_time: slot.end_time.substring(0, 5)
            }));
            console.log('Обработанные слоты:', availableSlots.value);
        }
        
        selectedSlotId.value = null;
        selectedSlotTime.value = '';
    } catch (err) {
        console.error('Ошибка загрузки слотов:', err);
        bookingError.value = 'Ошибка загрузки доступного времени';
    } finally {
        loadingSlots.value = false;
    }
};


const selectSlot = (slot) => {
    selectedSlotId.value = slot.id;
    selectedSlotTime.value = slot.start_time;
};


const createAppointment = async () => {
    if (!selectedSlotId.value) {
        bookingError.value = 'Выберите время приема';
        return;
    }
    
    if (!selectedPriceId.value) {
        bookingError.value = 'Выберите услугу';
        return;
    }
    
    bookingError.value = '';
    
    try {
        const response = await fetch('/api/book-slot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({
                slot_id: selectedSlotId.value,
                price_id: selectedPriceId.value,
                reason: bookingReason.value
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const selectedDoctor = doctors.value.find(d => d.id_doctor == selectedDoctorId.value);
            const selectedPrice = prices.value.find(p => p.id_prices == selectedPriceId.value);
            
            lastCreatedAppointment.value = {
                id: Date.now(),
                doctor_name: `${selectedDoctor?.first_name} ${selectedDoctor?.last_name}`,
                service: selectedPrice?.description || 'Консультация',
                date: formatDateForDisplay(selectedDate.value),
                time: selectedSlotTime.value,
                price: selectedPrice?.price || 1500,
                reason: bookingReason.value || 'Не указана'
            };
            
            bookingSuccess.value = true;

            selectedDoctorId.value = '';
            selectedDate.value = '';
            selectedSlotId.value = null;
            selectedSlotTime.value = '';
            selectedPriceId.value = '';
            bookingReason.value = '';
            availableSlots.value = [];
            
            setTimeout(() => {
                bookingSuccess.value = false;
            }, 3000);
            
            router.reload({ only: ['appointments'] });
        } else {
            bookingError.value = data.error || 'Ошибка при записи';
        }
    } catch (err) {
        console.error('Ошибка:', err);
        bookingError.value = 'Ошибка соединения с сервером';
    }
};


const cancelAppointment = async (appointmentId) => {
    if (!confirm('Вы уверены, что хотите отменить запись?')) return;
    
    try {
        const response = await fetch(`/api/appointments/${appointmentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });
        
        if (response.ok) {
            alert('Запись отменена');
            router.reload({ only: ['appointments'] });
        } else {
            alert('Ошибка при отмене');
        }
    } catch (err) {
        console.error('Ошибка:', err);
        alert('Ошибка при отмене');
    }
};

const formatDateForDisplay = (date) => {
    if (!date) return '';
    const [year, month, day] = date.split('-');
    return `${day}.${month}.${year}`;
};

const minDate = new Date().toISOString().split('T')[0];

watch([selectedDoctorId, selectedDate], () => {
    loadAvailableSlots();
});

onMounted(() => {
    loadDoctors();
    loadPrices();
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    selectedDate.value = tomorrow.toISOString().split('T')[0];
});
</script>

<template>
    <AuthenticatedLayout>
        <Header/>
        
        <div class="dashboard-container">
            <div class="dashboard-content">
                <div class="welcome-card">
                    <h1 class="welcome-title">
                        Здравствуйте, {{ user?.first_name }} {{ user?.last_name }}!
                    </h1>
                </div>
                
                <div v-if="success" class="alert-success">
                    {{ success }}
                </div>
                

                <div class="booking-card">
                    <h2 class="section-title">Запись на прием</h2>
                    
                    <div v-if="bookingSuccess" class="booking-success">
                        Вы успешно записаны на прием!
                    </div>
                    
                    <div class="booking-form">
                        <div class="form-group">
                            <label>Выберите врача</label>
                            <select v-model="selectedDoctorId" :disabled="loadingDoctors">
                                <option value="">Выберите врача</option>
                                <option v-for="doctor in doctors" :key="doctor.id_doctor" :value="doctor.id_doctor">
                                    {{ doctor.first_name }} {{ doctor.last_name }} 
                                    (стаж {{ doctor.experience_years || 0 }} лет)
                                </option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Выберите дату</label>
                            <input type="date" v-model="selectedDate" :min="minDate" :disabled="!selectedDoctorId">
                        </div>
                        
                        <div v-if="selectedDoctorId && selectedDate" class="form-group">
                            <label>Выберите время</label>
                            <div v-if="loadingSlots" class="loading-slots">
                                <div class="spinner-small"></div> Загрузка...
                            </div>
                            <div v-else-if="availableSlots.length === 0" class="no-slots">
                                Нет свободного времени
                            </div>
                            <div v-else class="slots-grid">
                                <button v-for="slot in availableSlots" :key="slot.id"
                                    @click="selectSlot(slot)"
                                    :class="['slot-btn', { active: selectedSlotId === slot.id }]">
                                    {{ slot.start_time }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Выберите услугу</label>
                            <select v-model="selectedPriceId" :disabled="loadingPrices">
                                <option value="">Выберите услугу</option>
                                <option v-for="price in prices" :key="price.id_prices" :value="price.id_prices">
                                    {{ price.description }} - {{ price.price.toLocaleString() }} ₽
                                </option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Причина обращения (необязательно)</label>
                            <textarea v-model="bookingReason" rows="3" placeholder="Опишите вашу проблему..." class="form-textarea"></textarea>
                        </div>
                        
                        <div v-if="bookingError" class="error-message">
                            {{ bookingError }}
                        </div>
                        
                        <div class="form-actions">
                            <button class="btn-submit-booking" @click="createAppointment" :disabled="!selectedSlotId || !selectedPriceId">
                                Записаться на прием
                            </button>
                        </div>
                    </div>
                </div>
                
      
                <div class="appointments-card">
                    <h2 class="section-title">Мои записи</h2>
                    
  
                    <div v-if="appointments && appointments.length > 0">
                        <div v-for="appointment in appointments" :key="appointment.id" class="appointment-item">
                            <div class="appointment-info">
                                <div class="appointment-doctor">{{ appointment.doctor_name || 'Врач не указан' }}</div>
                                <div class="appointment-service">{{ appointment.service || 'Консультация' }}</div>
                                <div class="appointment-datetime">{{ appointment.date || 'Дата не указана' }} в {{ appointment.time || '--:--' }}</div>
                                <div class="appointment-price">{{ (appointment.price || 0).toLocaleString() }} ₽</div>
                                <div v-if="appointment.reason && appointment.reason !== 'Не указана'" class="appointment-reason">Причина: {{ appointment.reason }}</div>
                            </div>
                            <div class="appointment-actions">
                                <button @click="cancelAppointment(appointment.id)" class="btn-cancel">Отменить</button>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="(!appointments || appointments.length === 0) && !lastCreatedAppointment" class="empty-appointments">
                        <p>У вас пока нет активных записей</p>
                        <p class="empty-hint">Запишитесь на прием через форму выше</p>
                    </div>
                </div>
                
    
                <div class="info-grid">
                    <div class="user-info-card">
                        <h2 class="section-title">Мои данные</h2>
                        <div class="info-list">
                            <div class="info-item"><span class="label">Имя:</span><span class="value">{{ user?.first_name }}</span></div>
                            <div class="info-item"><span class="label">Фамилия:</span><span class="value">{{ user?.last_name }}</span></div>
                            <div class="info-item"><span class="label">Email:</span><span class="value">{{ user?.email }}</span></div>
                            <div class="info-item"><span class="label">Телефон:</span><span class="value">{{ user?.phone || 'Не указан' }}</span></div>
                        </div>
                    </div>
                    
                    <div class="review-card">
                        <h2 class="section-title">Оставить отзыв</h2>
                        <form @submit.prevent="submitReview">
                            <textarea v-model="reviewForm.review_text" rows="4" class="form-textarea" placeholder="Расскажите о вашем опыте..."></textarea>
                            <div v-if="reviewForm.errors.review_text" class="form-error">{{ reviewForm.errors.review_text }}</div>
                            <button type="submit" class="btn-submit" :disabled="reviewForm.processing">Отправить отзыв</button>
                        </form>
                    </div>
                </div>
                
                <div class="actions-card">
                    <div class="actions-grid">
                        <button @click="$inertia.post(route('logout'))" class="btn-danger">Выйти</button>
                    </div>
                </div>
            </div>
        </div>
        
    </AuthenticatedLayout>
    <Footer />
</template>

<style scoped>
.dashboard-container {
    padding: 2rem 0;
}

.dashboard-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.welcome-card {
    margin-bottom: 1.5rem;
}

.welcome-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}

.alert-success {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.booking-card {
    background: white;
    border-radius: 20px;
    border: 2px solid #265B87;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.booking-success {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    text-align: center;
    animation: fadeIn 0.5s;
}



.booking-form {
    width: 100%;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #374151;
}

.form-group select,
.form-group input[type="date"] {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #A4C3DD;
    border-radius: 12px;
    font-size: 1rem;
    background: white;
    cursor: pointer;
}

.form-group select:focus,
.form-group input:focus {
    outline: none;
    border-color: #265B87;
}

.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #A4C3DD;
    border-radius: 12px;
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
}

.form-textarea:focus {
    outline: none;
    border-color: #265B87;
}

.slots-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.slot-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #A4C3DD;
    border-radius: 8px;
    background: white;
    cursor: pointer;

}

.slot-btn:hover {
    border-color: #265B87;

}

.slot-btn.active {
    background: #265B87;
    color: white;
    border-color: #265B87;
}

.loading-slots {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    color: #6b7280;
}

.no-slots {
    text-align: center;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 12px;
    color: #6b7280;
}

.error-message {
    background: #fee2e2;
    color: #dc2626;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.form-actions {
    margin-top: 1rem;
}

.btn-submit-booking {
    width: 100%;
    padding: 0.875rem;
    background-color: #265B87;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;

}

.btn-submit-booking:hover:not(:disabled) {
    background-color: #113dff;
}

.btn-submit-booking:disabled {
background-color: #87888a;
    cursor: not-allowed;
}

.appointments-card {
    background: white;
    border-radius: 20px;
    border: 2px solid #A4C3DD;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.appointment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 2px solid #9c9c9c;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.appointment-item:last-child {
    margin-bottom: 0;
}


.appointment-badge {
    position: absolute;
    top: -10px;
    right: 10px;
    background: #22c55e;
    color: white;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}

.appointment-item {
    position: relative;
}

.appointment-info {
    flex: 1;
}

.appointment-doctor {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.appointment-service {
    font-size: 0.875rem;
    color: #475569;
}

.appointment-datetime {
    font-size: 0.875rem;
    color: #64748b;
}

.appointment-price {
    font-weight: 700;
    color: #265B87;
    margin-top: 0.25rem;
}

.appointment-reason {
    font-size: 0.875rem;
    color: #475569;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px dashed #e2e8f0;
}

.btn-cancel {
    background: #fee2e2;
    color: #dc2626;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid #dc2626;
    font-weight: bolder;
}

.btn-cancel:hover {
    background: #fecaca;
}

.empty-appointments {
    text-align: center;
    padding: 2rem;
    color: #64748b;
}

.empty-hint {
    font-size: 0.875rem;
    margin-top: 0.5rem;
    color: #94a3b8;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

@media (min-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr 1fr;
    }
}

.user-info-card, .review-card {
    background: white;
    border-radius: 20px;
    border: 2px solid #A4C3DD;
    padding: 1.5rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #265B87;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    gap: 1rem;
}

.label {
    width: 7rem;
    font-weight: 500;
    color: #64748b;
}

.value {
    color: #1e293b;
}

.btn-submit {
    width: 100%;
    margin-top: 1rem;
    padding: 0.75rem;
    background: #265B87;
    color: white;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;

}

.btn-submit:hover {
    background: #113dff;

}

.form-error {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.actions-card {
    background: white;
    border-radius: 20px;
    border: 2px solid #A4C3DD;
    padding: 1.5rem;
}

.actions-grid {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-outline, .btn-danger {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;

}

.btn-outline {
    background: #A4C3DD;
    border: 2px solid #265B87;
    color: #265B87;
}

.btn-outline:hover {
    background: white;

}

.btn-danger {
    background: #fee2e2;
    border: 2px solid #dc2626;
    color: #dc2626;
    font-weight: bolder;
}

.btn-danger:hover {
    background: #fecaca;

}
</style>