<template>
  <div>
    <nav>
      <MainMenu/>
    </nav>
    <div class="container">
      <main><RouterView/></main>
    </div>
  </div>
</template>

<script>
import MainMenu from './layout/MainMenu.vue'
export default {
  components: {
    MainMenu
  }
}
</script>
<style lang="scss" scoped>
body {
  height: 100vh;
}
</style>
