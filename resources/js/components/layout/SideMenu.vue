
<template>
  <aside :class="['side-menu', { closed }]">
    <nav>
      <div class="nav-toggle">
        <Button class="sm" @click="closed = !closed">
          {{ !closed ? '&#8592;' : '&#8594;' }}
        </Button>
      </div>
      <ul>
        <li :class="{ active: $route.name === '' }"><router-link to="/">Dashboard</router-link></li>
        <li :class="{ active: $route.name === 'crops' }"><router-link to="/crops">Crops</router-link></li>
        <li :class="{ active: $route.name === 'plants' }"><router-link to="/plants">Plants</router-link></li>
        <li :class="{ active: $route.name === 'plots' }"><router-link to="/plots-beds">Plots & Beds</router-link></li>
      </ul>
    </nav>
    <PlantForm v-if="newPlant" @close="newPlant = false"/>
    <PlotForm v-if="newPlot" @close="newPlot = false"/>
  </aside>
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
      newPlot: false,
      closed: true
    }
  },
  watch: {
    $route () {
      this.closed = true
    }
  }
}
</script>
<style lang="scss" scoped>
aside {
  flex: 0 0 200px;
  transition: all 0.3s;
  nav {
    background-color: $grey-800;
    position: fixed;
    top: 0px;
    left: 0;
    width: 100%;
    text-align: center;
    border-right: 1px solid $grey-500;
    height: 100%;
    transition: all 0.3s;
    .nav-toggle {
      position: absolute;
      top: 0;
      right: 0;
      transition: all 0.3s;
      font-size: 1em;
    }
    ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      margin-top: 25px;
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
  &.closed {
    flex: 0;
    nav {
      left: -100%;
    }
    .nav-toggle {
      position: absolute;
      left: 100%;
    }
  }
  @include device (desktop, 'all') {
    &.closed {
      flex: 0 0 200px;
      nav {
        left: unset;
      }
    }
    nav {
      background-color: transparent;
      width: unset;
      padding-right: 75px;
      text-align: left;
      .nav-toggle {
        display: none;
      }
    }
  }
}
</style>
