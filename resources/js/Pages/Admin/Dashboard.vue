<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    doctors: Array,
    services: Array,
    offers: Array,
    requests: Array,
    reviews: Array,
    prices: Array,
    stats: Object 
});

const activeTab = ref('doctors');
const loading = ref(false);
const error = ref(null);

const showAddModal = ref(false);
const showEditModal = ref(false);
const editingItem = ref(null);
const formData = ref({});

const offers = ref([]);
const doctors = ref([]);
const services = ref([]);
const requests = ref([]);
const reviews = ref([]);
const prices = ref([]);

const exportStats = () => {
    window.location.href = '/admin/stats/export';
};

const tabs = [
    { id: 'doctors', name: 'Врачи'},
    { id: 'services', name: 'Услуги'},
    { id: 'prices', name: 'Цены'},
    { id: 'offers', name: 'Акции'},
    { id: 'requests', name: 'Заявки'},
    { id: 'reviews', name: 'Отзывы'}
];

const fetchData = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        switch (activeTab.value) {
            case 'doctors':
                await fetchDoctors();
                break;
            case 'services':
                await fetchServices();
                break;
            case 'offers':
                await fetchOffers();
                break;
            case 'requests':
                await fetchRequests();
                break;
            case 'reviews':
                await fetchReviews();
                break;
            case 'prices':
                await fetchPrices();
                break;
        }
    } catch (err) {
        console.error('Ошибка загрузки:', err);
        error.value = 'Ошибка загрузки данных';
    } finally {
        loading.value = false;
    }
};

const fetchDoctors = async () => {
    const response = await fetch('/api/admin/doctors');
    const data = await response.json();
    doctors.value = data;
    
};

const fetchServices = async () => {
    const response = await fetch('/api/admin/services');
    services.value = await response.json();
};

const fetchOffers = async () => {
    const response = await fetch('/api/admin/special-offers');
    offers.value = await response.json();
};

const fetchRequests = async () => {
    const response = await fetch('/api/admin/requests');
    requests.value = await response.json();
};

const fetchReviews = async () => {
    const response = await fetch('/api/admin/reviews');
    const data = await response.json();
    reviews.value = data;
    
};

const fetchPrices = async () => {
    const response = await fetch('/api/admin/prices');
    const data = await response.json();
    prices.value = data;
    
};

const getEmptyForm = () => {
    switch (activeTab.value) {
        case 'doctors':
            return { first_name: '', last_name: '', email: '', phone: '', experience_years: '', date_of_birth: '', photo_url: '', password: ''};
        case 'services':
            return {
                name: '',
                description: '',
                price: ''
            };
        case 'offers':
            return {
                title: '',
                percent: '',
                desc: ''
            };
        case 'prices':
            return {
                category: '',
                description: '',
                price: ''
            };
        default:
            return {};
    }
};

const openAddModal = () => {
    formData.value = getEmptyForm();
    showAddModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    formData.value = { ...item };
    showEditModal.value = true;
};

const saveItem = async () => {
    loading.value = true;
    try {
        let url = `/api/admin/${getApiPath()}`;
        let body = { ...formData.value };
        
        if (activeTab.value === 'doctors') {
            delete body.id_doctor;
            delete body.id_doc;
        }
        if (activeTab.value === 'services') {
            delete body.id_services;
        }
        if (activeTab.value === 'offers') {
            delete body.id;
            delete body.id_specialOffers;
            if (body.description && !body.desc) {
                body.desc = body.description;
                delete body.description;
            }
        }
        if (activeTab.value === 'requests') {
            delete body.id_request;
        }
        if (activeTab.value === 'reviews') {
            delete body.idreviews;
        }
        if (activeTab.value === 'prices') {
            delete body.id_prices;
        }
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(body)
        });
        
        if (response.ok) {
            await fetchData();
            showAddModal.value = false;
            alert('Запись успешно добавлена!');
        } else {
            const errorData = await response.json();
            alert('Ошибка: ' + JSON.stringify(errorData.errors || errorData));
        }
    } catch (err) {
        console.error('Ошибка сохранения:', err);
        alert('Ошибка при сохранении');
    } finally {
        loading.value = false;
    }
};

const updateItem = async () => {
    loading.value = true;
    try {
        let id = editingItem.value.id_doctor || editingItem.value.id_doc || 
                 editingItem.value.id_services || editingItem.value.id_specialOffers || 
                 editingItem.value.id_request || editingItem.value.idreviews ||
                 editingItem.value.id_prices;
        
        let url = `/api/admin/${getApiPath()}/${id}`;
        let body = { ...formData.value };
        
        delete body.id_doctor;
        delete body.id_doc;
        delete body.id_services;
        delete body.id_specialOffers;
        delete body.id_request;
        delete body.idreviews;
        delete body.id;
        delete body.id_prices;
        delete body.created_at;
        delete body.updated_at;
        
        if (activeTab.value === 'offers' && body.description && !body.desc) {
            body.desc = body.description;
            delete body.description;
        }
        
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(body)
        });
        
        if (response.ok) {
            await fetchData();
            showEditModal.value = false;
            alert('Запись успешно обновлена!');
        } else {
            const errorData = await response.json();
            alert('Ошибка: ' + JSON.stringify(errorData.errors || errorData));
        }
    } catch (err) {
        alert('Ошибка при обновлении');
    } finally {
        loading.value = false;
    }
};

const deleteItem = async (id) => {
    if (!confirm('Вы уверены, что хотите удалить эту запись?')) return;
    
    loading.value = true;
    try {
        const response = await fetch(`/api/admin/${getApiPath()}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });
        
        if (response.ok) {
            await fetchData();
            alert('Запись удалена!');
        }
    } catch (err) {
        console.error('Ошибка удаления:', err);
        alert('Ошибка при удалении');
    } finally {
        loading.value = false;
    }
};

const publishReview = async (id) => {
    if (!confirm('Опубликовать этот отзыв на сайте?')) return;
    
    try {
        const response = await fetch(`/api/admin/reviews/${id}/publish`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });
        
        if (response.ok) {
            await fetchReviews();
            alert('Отзыв опубликован!');
        }
    } catch (err) {
        console.error('Ошибка публикации:', err);
        alert('Ошибка при публикации');
    }
};

const unpublishReview = async (id) => {
    if (!confirm('Снять отзыв с публикации?')) return;
    
    try {
        const response = await fetch(`/api/admin/reviews/${id}/unpublish`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });
        
        if (response.ok) {
            await fetchReviews();
            alert('Отзыв снят с публикации!');
        }
    } catch (err) {
        console.error('Ошибка:', err);
        alert('Ошибка при снятии с публикации');
    }
};

const getApiPath = () => {
    const paths = {
        doctors: 'doctors',
        services: 'services',
        offers: 'special-offers',
        requests: 'requests',
        reviews: 'reviews',
        prices: 'prices'
    };
    return paths[activeTab.value];
};

watch(activeTab, () => {
    fetchData();
});

onMounted(() => {
    fetchData();
});
</script>

<template>
    <div>
        <Header />
        
        <div class="admin-page">
            <div class="container">
                <div class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.totalDoctors || 0 }}</h3>
                                <p>Врачей</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.totalServices || 0 }}</h3>
                                <p>Услуг</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.totalOffers || 0 }}</h3>
                                <p>Акций</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.totalRequests || 0 }}</h3>
                                <p>Заявок</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.totalReviews || 0 }}</h3>
                                <p>Отзывов</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.totalAppointments || 0 }}</h3>
                                <p>Записей</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.totalUsers || 0 }}</h3>
                                <p>Пациентов</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.publishedReviews || 0 }}</h3>
                                <p>Опубликовано отзывов</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.unpublishedReviews || 0 }}</h3>
                                <p>На модерации</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.appointmentsToday || 0 }}</h3>
                                <p>Записей сегодня</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.appointmentsThisMonth || 0 }}</h3>
                                <p>Записей за месяц</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-info">
                                <h3>{{ stats?.newUsersThisMonth || 0 }}</h3>
                                <p>Новых пациентов</p>
                            </div>
                        </div>
                    
                    <div v-if="stats?.busyDoctor" class="busy-doctor-card">
                        <div class="busy-info">
                            <h3>Самый загруженный врач</h3>
                            <p>{{ stats.busyDoctor.first_name }} {{ stats.busyDoctor.last_name }}</p>
                            <span>{{ stats.busyDoctor.total }} записей</span>
                        </div>
                    </div>
                    </div>
                    <button @click="exportStats" class="export-btn">
                        Скачать Excel
                    </button>
                    
                </div>
                
                <h1 class="page-title">Панель администратора</h1>
                
                <div class="tabs">
                    <button 
                        v-for="tab in tabs" 
                        :key="tab.id"
                        :class="{ active: activeTab === tab.id }"
                        @click="activeTab = tab.id"
                    >
                        {{ tab.name }}
                    </button>
                </div>
                
                <div v-if="error" class="error-message">
                    {{ error }}
                    <button @click="fetchData" class="retry-btn">Повторить</button>
                </div>

                <div v-if="loading" class="loading">

                    <p>Загрузка...</p>
                </div>

                <div v-else class="tab-content">
                    
                    
                    <div v-if="activeTab === 'doctors'" class="section">
                        <div class="section-header">
                            <button @click="openAddModal" class="btn-add">Добавить врача</button>
                        </div>
                        
                        <div v-if="doctors.length === 0" class="empty-state">Нет врачей. Нажмите "Добавить врача"</div>
                        
                        <div v-else class="cards-grid">
                            <div v-for="doctor in doctors" :key="doctor.id_doctor" class="card doctor-card">
                                <h3 class="card-title">{{ doctor.first_name }} {{ doctor.last_name }}</h3>
                                <p class="doctor-experience">Стаж: {{ doctor.experience_years || 0 }} лет</p>
                                <div class="doctor-contact" v-if="doctor.email || doctor.phone">
                                    <div v-if="doctor.email">{{ doctor.email }}</div>
                                    <div v-if="doctor.phone">{{ doctor.phone }}</div>
                                </div>
                                <div class="card-actions">
                                    <button @click="openEditModal(doctor)" class="btn-edit">Редактировать</button>
                                    <button @click="deleteItem(doctor.id_doctor)" class="btn-delete">Удалить</button>
                                </div>
                            </div>
                        </div>
                    </div>

                
                    <div v-if="activeTab === 'services'" class="section">
                        <div class="section-header">
                            <button @click="openAddModal" class="btn-add">Добавить услугу</button>
                        </div>
                        
                        <div v-if="services.length === 0" class="empty-state">Нет услуг. Нажмите "Добавить услугу"</div>
                        
                        <div v-else class="services-list">
                            <div v-for="service in services" :key="service.id_services" class="service-row">
                                <div class="service-info">
                                    <div class="service-name">{{ service.name }}</div>
                                    <div class="service-description">{{ service.description }}</div>
                                </div>
                                <div class="service-price">{{ Number(service.price).toLocaleString('ru-RU') }} ₽</div>
                                <div class="service-actions">
                                    <button @click="openEditModal(service)" class="btn-edit">Редактировать</button>
                                    <button @click="deleteItem(service.id_services)" class="btn-delete">Удалить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
               
                    <div v-if="activeTab === 'prices'" class="section">
                        <div class="section-header">
                            <button @click="openAddModal" class="btn-add">Добавить цену</button>
                        </div>
                        
                        <div v-if="prices.length === 0" class="empty-state">Нет цен. Нажмите "Добавить цену"</div>
                        
                        <div v-else class="prices-list">
                            <div v-for="price in prices" :key="price.id_prices" class="price-row">
                                <div class="price-info">
                                    <div class="price-category">{{ price.category }}</div>
                                    <div class="price-description">{{ price.description }}</div>
                                </div>
                                <div class="price-value">{{ Number(price.price).toLocaleString('ru-RU') }} ₽</div>
                                <div class="price-actions">
                                    <button @click="openEditModal(price)" class="btn-edit">Редактировать</button>
                                    <button @click="deleteItem(price.id_prices)" class="btn-delete">Удалить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                 
                    <div v-if="activeTab === 'offers'" class="section">
                        <div class="section-header">
                            <button @click="openAddModal" class="btn-add">Добавить акцию</button>
                        </div>
                        
                        <div v-if="offers.length === 0" class="empty-state">Нет акций. Нажмите "Добавить акцию"</div>
                        
                        <div v-else class="cards-grid">
                            <div v-for="offer in offers" :key="offer.id_specialOffers" class="card offer-card">
                                <div class="offer-badge">-{{ offer.percent }}%</div>
                                <h3 class="card-title">{{ offer.title }}</h3>
                                <p class="card-description">{{ offer.desc }}</p>
                                <div class="card-actions">
                                    <button @click="openEditModal(offer)" class="btn-edit">Редактировать</button>
                                    <button @click="deleteItem(offer.id_specialOffers)" class="btn-delete">Удалить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
              
                    <div v-if="activeTab === 'requests'" class="section">
                        <div class="section-header">
                            <h2>Заявки на звонок</h2>
                        </div>
                        
                        <div v-if="requests.length === 0" class="empty-state">Нет заявок</div>
                        
                        <div v-else class="requests-list">
                            <div v-for="request in requests" :key="request.id_request" class="request-card">
                                <div class="request-info">
                                    <div class="request-name">{{ request.name }}</div>
                                    <div class="request-phone">{{ request.phone }}</div>
                                </div>
                                <div class="request-actions">
                                    <button @click="deleteItem(request.id_requests)" class="btn-delete">Удалить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div v-if="activeTab === 'reviews'" class="section">
                        <div class="section-header">
                            <h2>Отзывы пациентов</h2>
                        </div>
                        
                        <div v-if="reviews.length === 0" class="empty-state">Нет отзывов</div>
                        
                        <div v-else class="reviews-list">
                            <div v-for="review in reviews" :key="review.idreviews" class="review-card">
                                <div class="review-header">
                                    <span class="review-user">{{ review.user?.first_name || 'Пользователь' }} {{ review.user?.last_name || '' }}</span>
                                    <span :class="['review-status', review.is_published ? 'published' : 'unpublished']">
                                        {{ review.is_published ? 'Опубликован' : 'На модерации' }}
                                    </span>
                                    <span class="review-date">{{ new Date(review.created_at).toLocaleDateString('ru-RU') }}</span>
                                </div>
                                <p class="review-text">{{ review.review_text }}</p>
                                <div class="review-actions">
                                    <button v-if="!review.is_published" @click="publishReview(review.idreviews)" class="btn-publish">Опубликовать</button>
                                    <button v-if="review.is_published" @click="unpublishReview(review.idreviews)" class="btn-unpublish">Снять с публикации</button>
                                    <button @click="deleteItem(review.idreviews)" class="btn-delete">Удалить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <div v-if="showAddModal" class="modal" @click.self="showAddModal = false">
            <div class="modal-content">
                <form @submit.prevent="saveItem">
                    <template v-if="activeTab === 'doctors'">
                        <div class="form-row">
                            <div class="form-group"><label>Имя *</label><input v-model="formData.first_name" type="text" required></div>
                            <div class="form-group"><label>Фамилия *</label><input v-model="formData.last_name" type="text" required></div>
                        </div>
                        <div class="form-group"><label>Email *</label><input v-model="formData.email" type="email" required></div>
                        <div class="form-group"><label>Телефон</label><input v-model="formData.phone" type="tel"></div>
                        <div class="form-group"><label>Стаж (лет)</label><input v-model.number="formData.experience_years" type="number" min="0"></div>
                        <div class="form-group"><label>Дата рождения</label><input v-model="formData.date_of_birth" type="date"></div>
                        <div class="form-group"><label>Фото (URL)</label><input v-model="formData.photo_url" type="url"></div>
                        <div class="form-group"><label>Пароль *</label><input v-model="formData.password" type="password" required></div>
                    </template>
                    
                    <template v-if="activeTab === 'services'">
                        <div class="form-group"><label>Название услуги *</label><input v-model="formData.name" type="text" required></div>
                        <div class="form-group"><label>Описание *</label><textarea v-model="formData.description" rows="2" required></textarea></div>
                        <div class="form-group"><label>Цена (₽) *</label><input v-model.number="formData.price" type="number" min="0" required></div>
                    </template>
                    
                    <template v-if="activeTab === 'prices'">
                        <div class="form-group"><label>Категория *</label><input v-model="formData.category" type="text" required></div>
                        <div class="form-group"><label>Описание *</label><textarea v-model="formData.description" rows="2" required></textarea></div>
                        <div class="form-group"><label>Цена (₽) *</label><input v-model.number="formData.price" type="number" min="0" required></div>
                    </template>
                    
                    <template v-if="activeTab === 'offers'">
                        <div class="form-group"><label>Название акции *</label><input v-model="formData.title" type="text" required></div>
                        <div class="form-group"><label>Скидка (%) *</label><input v-model.number="formData.percent" type="number" min="0" max="100" required></div>
                        <div class="form-group"><label>Описание *</label><textarea v-model="formData.description" rows="3" required></textarea></div>
                    </template>
                    
                    <div class="modal-buttons">
                        <button type="submit" class="btn-save">Сохранить</button>
                        <button type="button" @click="showAddModal = false" class="btn-cancel">Отмена</button>
                    </div>
                </form>
            </div>
        </div>


        <div v-if="showEditModal" class="modal" @click.self="showEditModal = false">
            <div class="modal-content">
                <form @submit.prevent="updateItem">
                    <template v-if="activeTab === 'doctors'">
                        <div class="form-row">
                            <div class="form-group"><label>Имя</label><input v-model="formData.first_name" type="text" required></div>
                            <div class="form-group"><label>Фамилия</label><input v-model="formData.last_name" type="text" required></div>
                        </div>
                        <div class="form-group"><label>Email</label><input v-model="formData.email" type="email" required></div>
                        <div class="form-group"><label>Телефон</label><input v-model="formData.phone" type="tel"></div>
                        <div class="form-group"><label>Стаж (лет)</label><input v-model.number="formData.experience_years" type="number" min="0"></div>
                        <div class="form-group"><label>Дата рождения</label><input v-model="formData.date_of_birth" type="date"></div>
                        <div class="form-group"><label>Фото (URL)</label><input v-model="formData.photo_url" type="url"></div>
                        <div class="form-group"><label>Новый пароль (оставьте пустым, если не меняете)</label><input v-model="formData.password" type="password"></div>
                    </template>
                    
                    <template v-if="activeTab === 'services'">
                        <div class="form-group"><label>Название услуги</label><input v-model="formData.name" type="text" required></div>
                        <div class="form-group"><label>Описание</label><textarea v-model="formData.description" rows="2" required></textarea></div>
                        <div class="form-group"><label>Цена (₽)</label><input v-model.number="formData.price" type="number" min="0" required></div>
                    </template>
                    
                    <template v-if="activeTab === 'prices'">
                        <div class="form-group"><label>Категория</label><input v-model="formData.category" type="text" required></div>
                        <div class="form-group"><label>Описание</label><textarea v-model="formData.description" rows="2" required></textarea></div>
                        <div class="form-group"><label>Цена (₽)</label><input v-model.number="formData.price" type="number" min="0" required></div>
                    </template>
                    
                    <template v-if="activeTab === 'offers'">
                        <div class="form-group"><label>Название акции</label><input v-model="formData.title" type="text" required></div>
                        <div class="form-group"><label>Скидка (%)</label><input v-model.number="formData.percent" type="number" min="0" max="100" required></div>
                        <div class="form-group"><label>Описание</label><textarea v-model="formData.description" rows="3" required></textarea></div>
                    </template>
                    
                    <div class="modal-buttons">
                        <button type="submit" class="btn-save">Обновить</button>
                        <button type="button" @click="showEditModal = false" class="btn-cancel">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <Footer />
</template>

<style scoped>
.admin-page {
    padding: 40px 0;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-title {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 25px;
    font-weight: 800;
    margin-bottom: 40px;
}

.tabs {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.tabs button {
    padding: 8px 28px;
    border: 2px solid #265B87;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
}

.tabs button:hover {
    background: #265B87;
    color: white;
}

.tabs button.active {
    background: #265B87;
    color: white;
}

.tab-content {
    border-radius: 20px;
    padding: 30px;
    background: white;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;

}

.section-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
}

.btn-add {
    background-color: #265B87;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-add:hover {
    background-color: #113dff;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
}

.card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    position: relative;
    border: 2px solid #A4C3DD;
}



.offer-badge {
    position: absolute;
    right: 20px;
    background: #265B87;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
}

.doctor-specialization {
    font-size: 17px;
    margin: 5px 0;
    color: #265B87;
}

.doctor-experience {
    font-size: 15px;
    margin: 5px 0;
    color: #64748b;
}

.doctor-contact {
    margin-top: 10px;
    font-size: 14px;
    color: #475569;
}

.card-title {
    font-size: 18px;
    font-weight: bold;
    margin: 5px 0;
    color: #1e293b;
}

.services-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.service-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border: 2px solid #265B87;
    border-radius: 15px;
    transition: all 0.3s;
}

.service-row:hover {
    background: #f8fafc;

}

.service-info {
    flex: 1;
}

.service-name {
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 8px;
    color: #1e293b;
}

.service-description {
    font-size: 14px;
    color: #64748b;
}

.service-price {
    font-size: 20px;
    font-weight: 700;
    color: #265B87;
    margin: 0 20px;
}

.requests-list, .reviews-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.request-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f8fafc;
    border-radius: 15px;
    border: 2px solid #A4C3DD;
}

.review-card {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 15px;
    border: 2px solid #265B87;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 13px;
}

.review-user {
    font-weight: 600;
    color: #1e293b;
}

.review-status {
    padding: 4px 12px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
}

.review-status.published {
    background: #D7E9F8;
    color: #06075f;
}

.review-status.unpublished {
    background: #D7E9F8;
    color: #ff0000;
}

.review-date {
    font-size: 12px;
    color: #64748b;
}

.review-text {
    margin: 0;
    line-height: 1.5;
    color: #334155;
}

.review-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
    flex-wrap: wrap;
}

.card-actions {
    display: flex;
    gap: 8px;
    margin-top: 15px;
    justify-content: flex-end;
}

.service-actions {
    display: flex;
    gap: 8px;
}

.btn-edit, .btn-delete, .btn-publish, .btn-unpublish {
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: all 0.2s;
}

.btn-edit {
    background-color: #bfe2ff;
    color: #265B87;
    border: 2px solid #265B87;
}

.btn-edit:hover {
    background-color: #265B87;
    color: white;
}

.btn-delete {
    color: #dc2626;
    background-color: #fee2e2;
    border: 2px solid #dc2626;
}

.btn-delete:hover {
    background-color: #dc2626;
    color: white;
}

.btn-publish {
    background-color: #bfe2ff;
    color: #265B87;
    border: 2px solid #265B87;
}

.btn-publish:hover {
    background-color: #265B87;
    color: white;
}

.btn-unpublish {
    background-color: #265B87;
    color: white;
    border: none;
}

.btn-unpublish:hover {
    background-color: rgb(76, 0, 255);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    background-color: rgba(164, 195, 221, 0.9);
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 20px;
    width: 90%;
    max-width: 550px;
    max-height: 90vh;
    overflow-y: auto;
    border: 2px solid #113dff;
}

.modal-content h3 {
    margin: 0 0 20px 0;
    color: #1f2937;
    font-size: 24px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #265B87;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #113dff;
    box-shadow: 0 0 0 3px rgba(17, 61, 255, 0.1);
}

.modal-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.btn-save {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    background-color: #265B87;
    color: white;
    transition: all 0.2s;
}

.btn-save:hover {
    background-color: #113dff;
}

.btn-cancel {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    background-color: #A4C3DD;
    color: #1e293b;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background-color: #8ba9c4;
}

.loading {
    text-align: center;
    padding: 60px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e2e8f0;
    border-top-color: #265B87;
    border-radius: 50%;
    margin: 0 auto 15px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.error-message {
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}



.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #64748b;
    font-size: 16px;
}



.stats-section {
    margin-bottom: 35px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 18px;
    border: 2px solid #A4C3DD;
}



.stat-info h3 {
    font-size: 26px;
    font-weight: 700;
    color: #265B87;
    line-height: 1.2;
}

.stat-info p {
    font-size: 15px;
    color: #000000;
    margin: 5px 0 0;
}

/* Карточка самого загруженного врача */
.busy-doctor-card {
    background: #e1f1ff;
    border: 2px solid #A4C3DD;
    border-radius: 16px;
    padding: 20px 25px;
    display: flex;
    align-items: center;
    gap: 20px;
}



.busy-info h3 {
    font-size: 15px;
    letter-spacing: 0.5px;
}

.busy-info p {
    font-size: 20px;
    font-weight: 700;
    margin: 0;

}

.busy-info span {
    font-size: 13px;
    display: inline-block;
    margin-top: 5px;
}

/* Адаптивность для статистики */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-card {
        padding: 15px;
        gap: 12px;
    }
    
    .stat-icon {
        font-size: 36px;
        min-width: 45px;
    }
    
    .stat-info h3 {
        font-size: 22px;
    }
    
    .busy-doctor-card {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .busy-icon {
        font-size: 38px;
    }
    
    .busy-info p {
        font-size: 18px;
    }
}

@media (max-width: 768px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }
    
    .service-row {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .service-price {
        margin: 10px 0;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .tabs button {
        padding: 8px 16px;
        font-size: 14px;
    }
    
    .page-title {
        font-size: 24px;
    }
    
    .review-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .review-actions {
        flex-wrap: wrap;
    }
    
    .modal-content {
        padding: 20px;
    }
}




.prices-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}


.price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    background: white;
    border: 2px solid #265B87;
    border-radius: 15px;

}




.price-info {
    flex: 1;
}


.price-category {
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 5px;
}


.price-description {
    font-size: 14px;
    color: #475569;
    line-height: 1.4;
}


.price-value {
    font-size: 22px;
    font-weight: 700;
    color: #265B87;
    margin: 0 25px;
    white-space: nowrap;
}


.price-actions {
    display: flex;
    gap: 10px;
}

@media (max-width: 768px) {
    .price-row {
        flex-direction: column;
        text-align: center;
        gap: 12px;
        padding: 18px;
    }
    
    .price-value {
        margin: 0;
    }
    
    .price-actions {
        justify-content: center;
    }
}

.export-btn {
    background: #265B87;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}
.export-btn:hover {
    background-color: #113dff;
}
</style>