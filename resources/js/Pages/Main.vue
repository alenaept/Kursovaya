<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ContactForm from '@/Components/ContactForm.vue';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const services = ref([]);
const doctors = ref([]);
const reviews = ref([]);
const loading = ref({
    services: true,
    doctors: true,
    reviews: true
});

const getPhotoUrl = (photoPath) => {
    if (!photoPath) {
        return '/storage/default-avatar.png'
    }
    if (photoPath.startsWith('http')) {
        return photoPath
    }
    if (photoPath.startsWith('/storage')) {
        return photoPath
    }
    return `/${photoPath}`
}

const getYearsWord = (years) => {
    const lastDigit = years % 10
    const lastTwoDigits = years % 100
    
    if (lastTwoDigits >= 11 && lastTwoDigits <= 14) {
        return 'лет'
    }
    if (lastDigit === 1) {
        return 'год'
    }
    if (lastDigit >= 2 && lastDigit <= 4) {
        return 'года'
    }
    return 'лет'
}

const handleImageError = (event) => {
    event.target.src = '/storage/default-avatar.png'
}

const loadServices = async () => {
    try {
        const response = await fetch('/api/public/services');
        const data = await response.json();
        services.value = data.slice(0, 4);
        console.log('Услуги загружены:', services.value);
    } catch (err) {
        console.error('Ошибка загрузки услуг:', err);
    } finally {
        loading.value.services = false;
    }
};

const loadDoctors = async () => {
    try {
        const response = await fetch('/api/public/doctors');
        const data = await response.json();
        doctors.value = data.slice(0, 4);
        console.log('Врачи загружены:', doctors.value);
    } catch (err) {
        console.error('Ошибка загрузки врачей:', err);
    } finally {
        loading.value.doctors = false;
    }
};

const loadReviews = async () => {
    try {
        const response = await fetch('/api/public/reviews');
        const data = await response.json();
        reviews.value = data.slice(0, 4);
        console.log('Отзывы загружены:', reviews.value);
    } catch (err) {
        console.error('Ошибка загрузки отзывов:', err);
    } finally {
        loading.value.reviews = false;
    }
};
const goToAbout = () => {
    router.visit('/about');
};
const goToServices = () => {
    router.visit('/services');
};

const goToDoctors = () => {
    router.visit('/specialists');
};

const goToReviews = () => {
    router.visit('/reviews');
};

onMounted(() => {
    loadServices();
    loadDoctors();
    loadReviews();
});
</script>

<template>
    <div>
        <Header />


        <section class="about">
            <div class="container">
                <div class="about-content">
                    <h1>Добро пожаловать в клинику</h1>
                    <p>Мы расположены в самом сердце города и гордимся тем, что предоставляем нашим пациентам самые современные и качественные стоматологические услуги в уютной и дружественной обстановке.</p>
                    <div class="about-buttons">
                        <button @click="goToAbout" class="about-btn">О нашей клинике</button>
                    </div>
                </div>
            </div>
        </section>

        <ContactForm />

        <section class="services">
            <div class="container">
                <h2>Услуги</h2>
                <div v-if="loading.services" class="loading-state">Загрузка услуг...</div>
                <div v-else-if="services.length === 0" class="empty-state">Нет услуг</div>
                <div v-else class="services-grid">
                    <div v-for="service in services" :key="service.id_services" class="service-card">
                        <div class="service-icon"></div>
                        <h3>{{ service.name }}</h3>
                        <p class="service-desc">{{ service.description?.substring(0, 80) }}{{ service.description?.length > 80 ? '...' : '' }}</p>
                    </div>
                </div>
                <div class="section-footer">
                    <button @click="goToServices" class="more-btn">Все услуги</button>
                </div>
            </div>
        </section>

        <section class="doctors">
            <div class="container">
                <h2>Врачи</h2>
                <div v-if="loading.doctors" class="loading-state">Загрузка врачей...</div>
                <div v-else-if="doctors.length === 0" class="empty-state">Нет врачей</div>
                <div v-else class="doctors-grid">
                    <div v-for="doctor in doctors" :key="doctor.id_doctor" class="doctor-card">
                        <div class="doctor-photo">
                            <img 
                                :src="getPhotoUrl(doctor.photo_url)" 
                                :alt="`${doctor.first_name} ${doctor.last_name}`"
                                @error="handleImageError"
                            >
                        </div>
                        <h3>{{ doctor.first_name }} {{ doctor.last_name }}</h3>
                        <p class="doctor-spec">{{ doctor.specialization || 'Стоматолог' }}</p>
                        <div class="doctor-exp">
                            Стаж: {{ doctor.experience_years || 0 }} {{ getYearsWord(doctor.experience_years || 0) }}
                        </div>
                    </div>
                </div>
                <div class="section-footer">
                    <button @click="goToDoctors" class="more-btn">Все врачи</button>
                </div>
            </div>
        </section>

        <section class="reviews">
            <div class="container">
                <h2>Отзывы</h2>
                <div v-if="loading.reviews" class="loading-state">Загрузка отзывов...</div>
                <div v-else-if="reviews.length === 0" class="empty-state">Нет отзывов</div>
                <div v-else class="reviews-grid">
                    <div v-for="review in reviews" :key="review.id" class="review-card">
                        <span class="review-author">{{ review.user_name }}</span>
                        <p class="review-text">{{ review.text }}</p>
                        
                    </div>
                </div>
                <div class="section-footer">
                    <button @click="goToReviews" class="more-btn">Все отзывы</button>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>

<style scoped>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

h2 {

    text-align: left;
    font-size: 23px;
    font-weight: bold;
    margin-bottom: 50px;
}


.about {
    margin-top: 40px;
    padding: 0 20px 0;
    background: #f8fafc;
}

.about-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;

}

.about-content h1 {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 20px;
}

.about-content p {
    font-size: 18px;
    line-height: 1.6;
    color: #475569;
    margin-bottom: 30px;
}

.about-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.about-btn {
    padding: 12px 32px;
    background: #265B87;
    color: white;
    border: none;
    border-radius: 15px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
        margin-bottom: 25px;
}

.about-btn:hover {
    background: #113dff;
}


.services {
    padding: 20px 0;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

.service-card {
    background: #EEF7FF;
    border-radius: 15px;
    border: 3px solid #A4C3DD;
    padding: 25px;
    text-align: center;
}

.service-card h3 {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 10px;
}

.service-desc {
    font-size: 18px;
    color: #64748b;
    margin-bottom: 10px;
    text-align: left;
}


.doctors {
    padding: 15px 0;
}

.doctors-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

.doctor-card {
    background: white;
    border-radius: 16px;
    border: 3px solid #A4C3DD;
    overflow: hidden;
}

.doctor-photo {
    width: 100%;
    height: 280px;
    overflow: hidden;
}

.doctor-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.doctor-card h3 {
    font-size: 18px;
    font-weight: 700;
    text-align: center;
    margin: 15px 0 5px;
}

.doctor-spec {
    font-size: 16px;
    text-align: center;
    margin-bottom: 5px;
}

.doctor-exp {
    font-size: 16px;
    text-align: center;
    padding-bottom: 20px;
}


.reviews {
    padding: 20px 0;
    background: #fff;
}

.reviews-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
}

.review-card {
    background: #f8fafc;
    border-radius: 16px;
    border: 2px solid #A4C3DD;
    padding: 25px;
}

.review-text {
    font-size: 16px;
    line-height: 1.5;
    margin-bottom: 15px;
    font-style: italic;
    color: #265B87;
}

.review-author {
    font-weight: bold;

    font-size: larger;
}

.section-footer {
    text-align: center;
    margin-top: 40px;
}

.more-btn {
    padding: 15px 55px;
    background: #265B87;
    color: white;
    border: none;
    border-radius: 15px;
    font-size: 16px;
    font-weight: 600;
}

.more-btn:hover {
    background: #113dff;
}


.loading-state, .empty-state {
    text-align: center;
    padding: 40px;
    color: #64748b;
}


@media (max-width: 1024px) {
    .services-grid,
    .doctors-grid,
    .reviews-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .services-grid,
    .doctors-grid,
    .reviews-grid {
        grid-template-columns: 1fr;
    }
    
    h2 {
        font-size: 28px;
    }
    
    .doctor-photo {
        height: 240px;
    }
}
</style>