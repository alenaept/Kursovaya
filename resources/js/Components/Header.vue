<template>
  <div>
    <header>
      <img :src="logoImage" alt="Логотип Happy Teeth">
      <nav>
        <ul>
          <li v-for="link in navLinks" :key="link.route">
            <Link 
              :href="link.path"
              :class="{ 'active': $page.url === link.path }"
            >
              {{ link.label }}
            </Link>
          </li>
        </ul>
      </nav>
    </header>


    <div class="header2">
      <img :src="logoImage" alt="Логотип Happy Teeth">
      <div class="menu-haver" @click="toggleMenu">
        <div class="bar" :class="{ 'change': isMenuOpen }"></div>
        <div class="bar" :class="{ 'change': isMenuOpen }"></div>
        <div class="bar" :class="{ 'change': isMenuOpen }"></div>
      </div>
      <div class="menu" :class="{ 'active': isMenuOpen }">
        <nav>
          <ul>
            <li v-for="link in navLinks" :key="link.route" @click="closeMenu">

              <Link 
                :href="link.path"
                :class="{ 'active': $page.url === link.path }"
              >
                {{ link.label }}
              </Link>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3' // ОБЯЗАТЕЛЬНО: импортируем Link из Inertia

const isMenuOpen = ref(false)

const logoImage = ref('/assets/images/ЛОГО.svg')

const navLinks = ref([
  { label: 'Главная', route: 'main', path: '/' }, // Обычно главная это '/'
  { label: 'Цены', route: 'price', path: '/price' },
  { label: 'Услуги', route: 'services', path: '/services' },
  { label: 'О клинике', route: 'about', path: '/about' },
  { label: 'Специалисты', route: 'specialists', path: '/specialists' },
  { label: 'Отзывы', route: 'reviews', path: '/reviews' },
  { label: 'Контакты', route: 'contacts', path: '/contacts' },
  { label: 'Акции', route: 'specialOffers', path: '/specialOffers' },
  { label: 'Личный кабинет', route: 'register', path: '/register' }
])

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}
</script>

<style scoped>

header {
  display: block;
  background: #A4C3DD;
  color: #000000;
  text-align: center;
  font-size: 20px;
}

header nav {
  background-color: var(--nav-bg-color, #A4C3DD);
  padding: 10px;
}

header nav ul {
  display: flex;
  justify-content: center;
  list-style: none;
  padding: 0;
  margin: 0;
}

header nav ul li a {
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 4px;
  transition: background-color 0.3s, color 0.3s;
}

header nav ul li a:hover {

  color: #0400ff;
}

header nav ul li a:active {

  color: #1900ff;
}

header nav ul li a.active {

  color: #1900ff;
  font-weight: bold;
}

header img {
  display: flex;
  margin-left: 45px;
  padding-top: 35px;
  width: 200px;
  height: auto;
}


.header2 {
  display: none !important;
  background-color: #A4C3DD;
  height: 135px;
  position: relative;
}

.header2 img {
  margin: 30px;
  height: 75px;
}

.menu-haver {
  cursor: pointer;
  display: flex;
  flex-direction: column;
  width: 40px;
  height: 50px;
  position: absolute;
  right: 45px;
  top: 45px;
  z-index: 2;
}

.bar {
  height: 5px;
color: black;
  margin: 4px 0;
  transition: all 0.3s ease;
  width: 100%;
}



.menu {
  background-color: white;
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  z-index: 1;
  box-sizing: border-box;
  padding: 10px;

}

.menu.active {
  display: block;
  animation: slideDown 0.3s ease;
}



.menu a {
  font-size: 20px;
}

.menu nav ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.menu nav ul li {
  padding: 17px 0;
  border-bottom: 1px solid #eee;
}

.menu nav ul li:last-child {
  border-bottom: none;
}

.menu nav ul li a {
  text-decoration: none;
  display: block;
  padding: 5px 10px;
  transition: all 0.3s ease;
}

.menu nav ul li a:hover {
  background-color: #A4C3DD;
  color: #1900ff;
  padding-left: 20px;
}

.menu nav ul li a.active {
  color: #1900ff;
  font-weight: bold;

}


@media (max-width: 768px) {
  header {
    display: none !important;
  }
  
  .header2 {
    display: block;
    margin-bottom: 15px;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  header nav ul li a {
    padding: 8px 12px;
    font-size: 16px;
  }
  
  header img {
    width: 150px;
  }
}
</style>