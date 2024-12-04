<template>
  <div :class="['card', size]">
    <div class="image">
      <img v-if="image" :src="image" alt="Plant"/>
      <img v-else src="https://via.placeholder.com/150" alt="Placeholder"/>
    </div>
    <div class="details">
      <h4>{{ title }}</h4>
      <p v-if="subtitle"><em>{{ subtitle }}</em></p>
      <p class="desc" v-html="description"></p>
      <slot name="content"></slot>
    </div>
    <div class="actions-contain">
      <slot name="actions"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Card',
  props: {
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String,
      default: ''
    },
    description: {
      type: String,
      default: ''
    },
    image: {
      type: String,
      default: ''
    },
    size: {
      type: String,
      default: 'md'
    }
  }
}
</script>

<style scoped lang="scss">
.card {
  display: flex;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 16px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  .details {
    flex: 1;
    padding-left: 16px;
    h4 {
      margin: 0;
    }
    p {
      margin: 0;
      &.desc {
        margin-top: 0.5em;
        font-size: $fontSize-s;
      }
    }
  }
  &.sm {
    img {
      width: 50px;
      height: 50px;
    }
  }
}
</style>