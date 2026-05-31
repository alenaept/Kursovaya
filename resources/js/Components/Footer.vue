<template>
  <footer class="main-footer">
    <div class="footer-container">
      <div class="footer-column logo-section">
        <div class="logo">
          <img src="/assets/images/ЛОГО.svg" alt="Логотип Happy Teeth">
        </div>
        <div class="social-icons">
          <a href="https://vk.com/happyteeth" target="_blank">
            <img src="/assets/images/VK.png" alt="VK">
          </a>
          <a href="https://t.me/happyteeth" target="_blank">
            <img src="\assets\images\telegram-no-bg-preview (carve.photos).png" alt="Telegram">
          </a>
        </div>
      </div>

      <div class="footer-column navigation-menu">
        <ul>
          <li v-for="link in navLinks" :key="link.path">
            <Link :href="link.path">{{ link.label }}</Link>
          </li>
        </ul>
      </div>

      <div class="footer-column contact-info">
        <p><strong>Клиника Happy Teeth</strong></p>
        <p>Адрес: 30 микрорайон, дом 3</p>
        <p>Телефон: +7 (3052) 58-42-95</p>
        <p>E-mail: Happyteethclinic@mail.ru</p>
        <p>Режим работы: пн-вск с 9-00 до 20-00</p>
        <p>Юр. адрес: 666969, г. Иркутск, мкр. 10, 50/50a</p>
      </div>
    </div>

    <div class="bottom-footer">
      &copy; {{ currentYear }} Стоматологическая клиника HAPPY Teeth. Все права защищены.
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const currentYear = computed(() => new Date().getFullYear())

const navLinks = [
  { label: 'Главная', path: '/' },
  { label: 'Цены', path: '/price' },
  { label: 'Услуги', path: '/services' },
  { label: 'О клинике', path: '/about' },
  { label: 'Специалисты', path: '/specialists' },
  { label: 'Отзывы', path: '/reviews' },
  { label: 'Контакты', path: '/contacts' },
  { label: 'Акции', path: '/specialOffers' },
  { label: 'Личный кабинет', path: '/dashboard' }
]
</script>

<style scoped>
.main-footer {
    background-color: #A4C3DD; 
    padding: 40px 20px;
    margin-top: 50px;
    clear: both; 
}

.footer-container {
    display: flex;
    justify-content: space-around; 
    align-items: flex-start; 
    max-width: 1200px; 
    margin: 0 auto; 
}

.footer-column {
    flex: 1; 
}

.logo img {
    width: 180px; 
    height: auto;
    margin-bottom: 20px;
}

.social-icons {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.social-icons img {
    width: 32px;
    height: 32px;
    transition: transform 0.3s;
}

.social-icons img:hover {
    transform: scale(1.1);
}

.navigation-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.navigation-menu li {
    margin-bottom: 8px;
}

.navigation-menu a {
    text-decoration: none;
    color: #1e293b;
    font-size: 16px;
    transition: color 0.3s;
}

.navigation-menu a:hover {
    color: #113dff;
}

.contact-info p {
    font-size: 14px;
    margin-bottom: 8px;
    color: #1e293b;
}

.contact-info p strong {
    font-size: 16px;
    color: #265B87;
}

.bottom-footer {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    font-size: 14px;
    color: #1e293b;
    border-top: 1px solid #8ba9c4;
}
</style>