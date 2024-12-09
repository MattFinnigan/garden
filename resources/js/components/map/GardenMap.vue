<template>
  <div class="garden-map">
    <div class="date-select">
      <Button class="sm" @click="removeDay"><</Button>
      <Input type="date" v-model="date" @change="fetchMaps"/>
      <Button class="sm" @click="addDay">></Button>
    </div>
    <div>
      <!-- <canvas ref="canvas"></canvas> -->
      <div class="map-container">
        <div v-for="(loc, l) in maps" :key="l">
          <h3>{{ loc.name }}</h3>
          <div v-for="(bed, b) in loc.beds" :key="b" class="bed-wrapper">
            <h4>{{ bed.name }} ({{ bed.l / 100 }}x{{ bed.w /100 }}m)</h4>
            <div class="bed" :style="bedStyle(bed)">
              <div v-for="(p, ce) in plantPositions(bed)" :key="ce" class="plant" :style="p.style">
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { fetchMaps } from '../../utils/api';

export default {
  name: 'GardenMap',
  data () {
    return {
      loading: true,
      date: new Date().toISOString().split('T')[0]
    }
  },
  mounted () {
    this.fetchMaps()
  },
  computed: {
    maps () {
      return this.$store.state.maps.list
    }
  },
  methods: {
    addDay () {
      const date = new Date(this.date)
      date.setDate(date.getDate() + 2)
      this.date = date.toISOString().split('T')[0]
      this.fetchMaps()
    },
    removeDay () {
      const date = new Date(this.date)
      date.setDate(date.getDate())
      this.date = date.toISOString().split('T')[0]
      this.fetchMaps()
    },
    fetchMaps () {
      this.loading = true
      fetchMaps(this, this.date).then(response => {
        this.loading = false
      })
    },
    bedStyle (bed) {
      return {
        width: `100%`,
        height: `${bed.w * 2}px`,
        position: "relative"
      }
    },
    plantPositions (bed) {
      const squareSize = 5 // Size of each square in px
      let x = 0
      let y = 0
      return bed.crop_entries.flatMap((entry) => {
        const result = []
        for (let i = 0; i < entry.qty; i++) {
          result.push({
            style: {
              width: `${(squareSize / bed.l) * 100}%`, // Relative width
              height: `${(squareSize / bed.w) * 100}%`, // Relative height
              top: `${(y / bed.w) * 100}%`, // Relative top
              left: `${(x / bed.l) * 100}%`, // Relative left
              position: "absolute",
              backgroundColor: "green",
              backgroundImage: `url(./images/${entry.crop.plant.image})`,
              backgroundSize: "contain",
              backgroundPosition: "center",
              backgroundRepeat: "no-repeat",
              border: "1px solid white",
            }
          })
          x += entry.area

          // Check if the square goes off the row
          if (x + squareSize > bed.l) {
            x = 0; // Reset x to the start of the row
            y += entry.area // Move to the next row
          }
        }
        return result;
      })
    }
  },
}
</script>

<style scoped lang=scss>
.garden-map {
  canvas {
    width: 100%;
  }
  .map-container {
    .bed {
      background: #2b2929;
      border: 1px solid gray;
    }
    .plant {
      position: absolute;
    }
  }
  .date-select {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1em;
    Button {
      margin: 0 0.5em;
    }
  }
}
</style>