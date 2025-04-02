<template>
  <i :class="[name, colour]" :style="{ width: size, height: size, maskSize }"></i>
</template>

<script>
export default {
  name: 'Icon',
  props: {
    name: {
      type: String,
      required: true
    },
    colour: {
      type: String,
      default: 'white'
    },
    size: {
      type: String,
      default: '12px'
    },
    maskSize: {
      type: String,
      default: '100%'
    }
  }
}
</script>

<style scoped lang="scss">
  i {
    display: inline-block;
    width: 1.1em;
    height: 1.1em;
    mask-repeat: no-repeat;
    vertical-align: middle;
    text-align: center;
    mask-size: 100%;
    &.white {
      background-color: $backgroundColour;
    }
    &.black {
      background-color: $textColour;
    }
    &.primary {
      background-color: $primary;
    }
    &.danger {
      background-color: $danger;
    }
    &.edit {
      mask-image: url('/images/edit.svg');
    }
    &.delete {
      mask-image: url('/images/delete.svg');
    }
    &.view {
      mask-image: url('/images/view.svg');
    }
    &.play {
      mask-image: url('/images/play-arrow.svg');
      &.reverse {
        transform: rotate(180deg);
      }
    }
    &.rewind {
      mask-image: url('/images/rewind.svg');
      &.reverse {
        transform: rotate(180deg);
      }
    }
    &.plus {
      mask-image: url('/images/plus.svg');
    }
    &.zoomin {
      mask-image: url('/images/zoom-in.svg');
    }
    &.zoomout {
      mask-image: url('/images/zoom-out.svg');
    }
    &.seedling {
      mask-image: url('/images/seedling.svg');
    }
    &.info {
      mask-image: url('/images/info.svg');
    }
  }
</style>