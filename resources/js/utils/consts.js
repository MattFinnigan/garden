
const defaultCrop = (plant = null) => {
  return {
    plant_id: plant?.id || null,
    days_to_harvest: plant?.days_to_harvest || 1,
    qty: 1,
    spacing: plant?.spacing || 1,
    x: 0,
    y: 0,
    height: 1,
    width: 1,
    crop_entries: [],
    startMonth: 1,
    endMonth: null
  }
}

const defaultCropEntry = (bed = null, crop = null) => {
  return {
    action: 'Planned',
    stage: 'Planned',
    notes: null,
    images: [],
    datetimestamp: new Date().toISOString().slice(0, 16),
    crop_id: crop?.id || null,
  }
}

const defaultBed = () => {
  return {
    x: 0,
    y: 0,
    width: 0,
    height: 0,
    name: '',
    description: '',
    images: []
  }
}

const defaultPlant = () => {
  return {
    name: '',
    variety: '',
    description: '',
    spacing: 1,
    sow_from: 1,
    sow_to: 1,
    days_to_harvest: 1,
    image: ''
  }
}

const monthOptionsLong = () => {
  return [
    { value: 1, label: 'January' },
    { value: 2, label: 'February' },
    { value: 3, label: 'March' },
    { value: 4, label: 'April' },
    { value: 5, label: 'May' },
    { value: 6, label: 'June' },
    { value: 7, label: 'July' },
    { value: 8, label: 'August' },
    { value: 9, label: 'September' },
    { value: 10, label: 'October' },
    { value: 11, label: 'November' },
    { value: 12, label: 'December' }
  ]
}

const monthsShort = () => {
  return {
    1: 'Jan',
    2: 'Feb',
    3: 'Mar',
    4: 'Apr',
    5: 'May',
    6: 'Jun',
    7: 'Jul',
    8: 'Aug',
    9: 'Sep',
    10: 'Oct',
    11: 'Nov',
    12: 'Dec'
  }
}

export {
  defaultCrop,
  defaultCropEntry,
  defaultBed,
  defaultPlant,
  monthOptionsLong,
  monthsShort
}