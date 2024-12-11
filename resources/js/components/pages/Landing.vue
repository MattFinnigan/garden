<template>
  <div class="landing">
    <h2>Dashboard</h2>
    <div class="garden-map">
      <GardenMap/>
    </div>
  </div>
</template>

<script>
import GardenMap from '../map/GardenMap.vue'
export default {
  name: 'Landing',
  components: {
    GardenMap
  },
}
</script>

<style scoped lang="scss">
.landing {
}
</style>