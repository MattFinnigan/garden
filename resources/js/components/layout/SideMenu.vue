
<template>
  <div class="side-menu">
    <nav>
      <ul>
        <li :class="{ active: $route.name === '' }"><router-link to="/">Dashboard</router-link></li>
        <li :class="{ active: $route.name === 'crops' }"><router-link to="/crops">Crops</router-link></li>
        <li :class="{ active: $route.name === 'plants' }"><router-link to="/plants">Plants</router-link></li>
        <li :class="{ active: $route.name === 'plots' }"><router-link to="/plots-beds">Plots & Beds</router-link></li>
      </ul>
    </nav>
    <PlantForm v-if="newPlant" @close="newPlant = false"/>
    <PlotForm v-if="newPlot" @close="newPlot = false"/>
  </div>
</template>

<script>
import PlantForm from '../forms/PlantForm.vue';
import PlotForm from '../forms/PlotForm.vue';

export default {
  components: {
    PlantForm,
    PlotForm
  },
  data () {
    return {
      newPlant: false,
      newPlot: false
    }
  }
}
</script>
<style lang="scss" scoped>
nav {
  ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    li {
      margin-bottom: 1rem;
      a, :deep(button.link) {
        color: $textColour;
        &:hover {
          color: $linkColour;
        }
      }
      &.active {
        a {
          color: $linkColour;
        }
      }
    }
  }
}
</style>
