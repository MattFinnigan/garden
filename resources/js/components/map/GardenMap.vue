<template>
  <div class="garden-map">
    {{ date }}
    <div v-if="!loading">
      <canvas ref="canvas"></canvas>
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
    fetchMaps(this, this.date).then(() => {
      this.loading = false
      this.$nextTick(() => {
        const canvas = this.$refs.canvas
        const ctx = canvas.getContext('2d')
        // ctx.fillStyle = 'red'
        // ctx.fillRect(0, 0, 150, 100)
        // ctx.fillStyle = 'blue'
        // ctx.fillRect(10, 10, 10, 10)
        for (const l in this.maps) {
          const loc = this.maps[l]
          let x = 0
          let y = 5
          for (const b in loc.beds) {
            const bed = loc.beds[b]
            const bedX = (bed.l * 2) + 5
            const bedY = (bed.w * 2) + 5
            ctx.fillStyle = 'green'
            ctx.fillRect(0, 0, bedX, bedY)
            for (const ce in bed.crop_entries) {
              const entry = bed.crop_entries[ce]
              for (let i = 0; i < entry.qty; i++) {
                if (x + 5 > bedX) {
                  x = 0
                  y += 5
                }
                ctx.fillStyle = 'blue'
                ctx.fillRect(x + 5, y, entry.area + 5, entry.area + 5)
                x += entry.area * 2
              }
            }
          }
        }
      })
    })
  },
  computed: {
    maps () {
      return this.$store.state.maps.list
    },
  },
  methods: {
  },
}
</script>

<style scoped>
.garden-map {
  canvas {
    width: 100%;
  }
}
</style>