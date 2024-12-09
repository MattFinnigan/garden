<template>
  <div class="garden-map">
    {{ date }}
    <div v-if="!loading">
      <!-- <canvas ref="canvas"></canvas> -->
      <div class="map-container">
        <div v-for="(loc, l) in maps" :key="l">
          <div v-for="(bed, b) in loc.beds" :key="b" class="bed" :style="bedStyle(bed)">
            <div v-for="(p, ce) in plantPositions(bed)" :key="ce" class="plant" :style="p.style">
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
      date: '2024-12-07'
    }
  },
  mounted () {
    fetchMaps(this, this.date).then(() => {
      this.loading = false
    })
  },
  computed: {
    maps () {
      return this.$store.state.maps.list
    }
  },
  methods: {
    bedStyle (bed) {
      const { l, w } = bed
      return {
        width: `100%`,
        height: `300px`,
        position: "relative"
      }
    },
    plantPositions (bed) {
      const squareSize = 5 // Size of each square in px
      let x = 0
      let y = 0
      const bedWidth = bed.l;
      return bed.crop_entries.flatMap((entry) => {
        const result = []
        for (let i = 0; i < entry.qty; i++) {
          result.push({
            style: {
              width: `${(squareSize / bedWidth) * 100}%`, // Relative width
              height: `${(squareSize / bed.w) * 100}%`, // Relative height
              top: `${(y / bed.w) * 100}%`, // Relative top
              left: `${(x / bedWidth) * 100}%`, // Relative left
              position: "absolute",
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
      background-color: green;
    }
  }
}
</style>