<template>
  <div class="table-component">
    <div v-if="header" class="table-header">
      <slot name="header"></slot>
    </div>
    <div v-if="prefix" class="table-prefix">
      <slot name="prefix"></slot>
    </div>
    <table>
      <thead>
        <tr>
          <th v-for="header in headers" :key="header">{{ header.label }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, i) in rows" :key="row.id || i">
          <td v-for="header in headers" :key="header.key">
            <span>{{ row[header.key] }}</span>
          </td>
          <td v-if="actions">
            <Button v-if="actions.edit" classes="sm" @click="$emit('edit', row, i)">Edit</Button>
            <Button v-if="actions.delete" classes="sm" @click="$emit('delete', row, i)">Delete</Button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'TableComponent',
  props: {
    headers: Array,
    rows: Array,
    actions: Object
  },
  computed: {
    header() {
      return !!this.$slots.header
    },
    prefix() {
      return !!this.$slots.prefix
    }
  }
}
</script>

<style scoped lang="scss">
.table-component {
  .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid $grey-500;
    border-radius: 0.5em;
    thead {
      background-color: $grey-700;
      border-bottom: 1px solid $grey-500;
      th {
        padding: 10px;
        text-align: left;
        font-size: $fontSize-s;
      }
    }
    tbody {
      tr {
        &:nth-child(even) {
          background-color: $grey-800;
        }
        td {
          padding: 10px;
          font-size: $fontSize-s;
        }
      }
    }
  }
}
</style>