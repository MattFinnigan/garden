<template>
  <div class="grid" ref="grid" :style="{ backgroundSize: `${sqSize}px ${sqSize}px` }">
    <div v-if="!!$slots.form" class="form"><slot name="form"></slot></div>
    <div
      v-if="newBox"
      class="grid-item"
      :style="{ top: newBox.y + 'px', left: newBox.x + 'px', width: newBox.l + 'px', height: newBox.w + 'px' }"></div>
    <div
      v-for="item in items"
      class="grid-item"
      :style="{ top: item.y + 'px', left: item.x + 'px', width: item.l + 'px', height: item.w + 'px' }">
    </div>
  </div>
</template>
<script>
export default {
  name: 'Grid',
  props: {
    items: {
      type: Array,
      required: true
    }
  },
  emits: ['newBox'],
  data () {
    return {
      sqSize: 50,
      mouseDown: null,
      newBox: null
    }
  },
  mounted () {
    this.$refs.grid.addEventListener('mousedown', (e) => {
      const rect = e.target.getBoundingClientRect()
      const x = e.clientX - rect.left
      const y = e.clientY - rect.top
      this.mouseDown = { x, y }
      console.log('mousedown', this.mouseDown)
    })
    this.$refs.grid.addEventListener('mouseup', (e) => {
      const rect = e.target.getBoundingClientRect()
      const x = e.clientX - rect.left
      const y = e.clientY - rect.top
      console.log('mouseup', { x, y })
      // check if diff is more than sqSize
      if (this.mouseDown && Math.abs(x - this.mouseDown.x) > (this.sqSize / 2) || Math.abs(y - this.mouseDown.y) > (this.sqSize / 2)) {
        console.log('dragged')
        this.setBox(this.mouseDown, { x, y })
        this.mouseDown = null
      } else {
        console.log('clicked')
      }
    })
  },
  methods: {
    setBox (start, end) {
      this.newBox = {
        x: Math.min(start.x, end.x),
        y: Math.min(start.y, end.y),
        l: Math.abs(start.x - end.x),
        w: Math.abs(start.y - end.y)
      }
      this.$emit('newBox', this.newBox)
    }
  }
}
</script>

<style scoped lang="scss">
.grid {
  position: relative;
  height: 100vh;
  width: 100%;
  background-image:
    repeating-linear-gradient($grey-400 0 1px, transparent 1px 100%),
    repeating-linear-gradient(90deg, $grey-400 0 1px, transparent 1px 100%);
  .grid-item {
    border: 1px solid $grey-600;
    position: absolute;
  }
}
</style>