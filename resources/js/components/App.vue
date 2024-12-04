<template>
  <div id="app">
    <nav>
      <MainMenu/>
    </nav>
    <div class="container">
      <SideMenu/>
      <main><RouterView/></main>
    </div>
  </div>
</template>

<script>
import MainMenu from './layout/MainMenu.vue';
import SideMenu from './layout/SideMenu.vue';

export default {
  components: {
    MainMenu,
    SideMenu
  }
}
</script>
<style lang="scss" scoped>
#app {
  background-color: $black;
  min-height: 100vh;
  color: $textColour;
  .container {
    display: flex;
    position: relative;
    main {
      flex: 1;
    }
  }
}
</style>
