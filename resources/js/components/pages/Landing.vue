<template>
  <div class="landing">
    <SideMenu/>
    <div>
      <GardenMap/>
    </div>
  </div>
</template>

<script>
import SideMenu from '../layout/SideMenu.vue';
import GardenMap from '../map/GardenMap.vue'
export default {
  name: 'Landing',
  components: {
    GardenMap,
    SideMenu
  },
}
</script>

<style scoped lang="scss">
.landing {
  display: flex;
  > aside {
    flex: 0 0 200px;
  }
  > div {
    flex: 1;
  }
}
</style>