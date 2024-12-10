<template>
  <div class="landing">
    <div class="header-contain">
      <h1>Dashboard</h1>
    </div>
    <GardenGrid/>
  </div>
</template>

<script>
import GardenGrid from '../grid/GardenGrid.vue';

export default {
  name: 'Landing',
  components: {
    GardenGrid
  }
}
</script>

<style scoped lang="scss">
.header-contain {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1em;
  h1 {
    margin: 0;
  }
}

</style>