<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchServices = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/public/services');
        services.value = response.data || [];
        error.value = null;
    } catch (err) {
        console.error('Ошибка загрузки услуг:', err);
        error.value = 'Не удалось загрузить список услуг.';
        services.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchServices();
});
</script>

<template>
    <div>
        <Header />

        <div class="services-page">
            <div class="container">
                <h1>Наши услуги</h1>

                <div v-if="loading" class="loading">
                    Загрузка...
                </div>

                <div v-else-if="error" class="error">
                    {{ error }}
                </div>

                <div v-else-if="services.length === 0" class="empty">
                    Нет услуг
                </div>

                <div v-else class="services-list">
                    <div 
                        v-for="service in services" 
                        :key="service.id_services"
                        class="service-card"
                    >
                        <h3>{{ service.name }}</h3>
                        <p>{{ service.description }}</p>
                        <div class="price">{{ Number(service.price).toLocaleString('ru-RU') }} ₽</div>
                    </div>
                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>

<style scoped>
.services-page {
    padding: 30px 0;
    background: #f8fafc;
}

.container {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 20px;
}

h1 {
    text-align: center;
    font-size: 25px;
    font-weight: bold;
    margin-bottom: 40px;
    color: #1e293b;
}

.services-list {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.service-card {
    background: white;
    border-radius: 20px;
    padding: 35px;
    border: 2px solid #A4C3DD;
}

.service-card h3 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #265B87;
}

.service-card p {
    font-size: 18px;
    line-height: 1.6;
    color: #475569;
    margin-bottom: 20px;
}

.price {
    font-size: 22px;
    font-weight: bold;
    color: #265B87;
}

.loading, .error, .empty {
    text-align: center;
    padding: 40px;
    color: #64748b;
}

.error {
    color: #dc2626;
}


@media (max-width: 768px) {
    .service-card {
        padding: 25px;
    }
    
    .service-card h3 {
        font-size: 20px;
    }
    
    .service-card p {
        font-size: 16px;
    }
    
    .price {
        font-size: 18px;
    }
    
    h1 {
        font-size: 28px;
    }
}
</style>