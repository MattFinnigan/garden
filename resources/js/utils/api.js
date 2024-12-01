import axios from 'axios'

const axiosInstance = axios.create({
  withCredentials: true,
})

export const get = (url) => {
  return new Promise((resolve, reject) => {
    axiosInstance.get('/api/' + url).then((response) => {
      resolve(response)
    }).catch((error) => {
      console.error(error)
      reject(error)
    })
  })
}
export const post = (url, data) => {
  return new Promise((resolve, reject) => {
    axiosInstance.post('/api/' + url, data).then((response) => {
      resolve(response)
    }).catch((error) => {
      console.error(error)
      reject(error)
    })
  })
}
export const destroy = (url) => {
  return new Promise((resolve, reject) => {
    axiosInstance.delete('/api/' + url).then((response) => {
      resolve(response)
    }).catch((error) => {
      console.error(error)
      reject(error)
    })
  })
}

// plants
export const fetchPlants = () => {
  return new Promise((resolve) => {
    get('plants').then((response) => {
      console.log('fetchPlants', 'response', response)
      resolve(response)
    })
  })
}
export const fetchPlant = (id) => {
  return new Promise((resolve) => {
    get(`plants/${id}`).then((response) => {
      console.log('fetchPlant', id, 'response', response)
      resolve(response)
    })
  })
}
export const createPlant = (data) => {
  return new Promise((resolve) => {
    post('plants', data).then((response) => {
      console.log('createPlant', data, 'response', response)
      resolve(response)
    })
  })
}
export const updatePlant = (id, data) => {
  return new Promise((resolve) => {
    post(`plants/${id}`, data).then((response) => {
      console.log('updatePlant', id, data, 'response', response)
      resolve(response)
    })
  })
}
export const deletePlant = (id) => {
  return new Promise((resolve) => {
    destroy(`plants/${id}`).then((response) => {
      console.log('deletePlant', id, 'response', response)
      resolve(response)
    })
  })
}

// crops
export const fetchCrops = () => {
  return new Promise((resolve) => {
    get('crops').then((response) => {
      console.log('fetchCrops', 'response', response)
      resolve(response)
    })
  })
}
export const fetchCrop = (id) => {
  return new Promise((resolve) => {
    get(`crops/${id}`).then((response) => {
      console.log('fetchCrop', id, 'response', response)
      resolve(response)
    })
  })
}
export const createCrop = (data) => {
  return new Promise((resolve) => {
    post('crops', data).then((response) => {
      console.log('createCrop', data, 'response', response)
      resolve(response)
    })
  })
}
export const updateCrop = (id, data) => {
  return new Promise((resolve) => {
    post(`crops/${id}`, data).then((response) => {
      console.log('updateCrop', id, data, 'response', response)
      resolve(response)
    })
  })
}
export const deleteCrop = (id) => {
  return new Promise((resolve) => {
    destroy(`crops/${id}`).then((response) => {
      console.log('deleteCrop', id, 'response', response)
      resolve(response)
    })
  })
}

// crop events
export const deleteCropEvent = (id) => {
  return new Promise((resolve) => {
    destroy(`crop-history/${id}`).then((response) => {
      console.log('deleteCropEvent', id, 'response', response)
      resolve(response)
    })
  })
}

export const createCropEvent = (cropId, data) => {
  return new Promise((resolve) => {
    post(`crop-history/create/${cropId}`, data).then((response) => {
      console.log('createCropEvent', cropId, data, 'response', response)
      resolve(response)
    })
  })
}

export const updateCropEvent = (id, data) => {
  return new Promise((resolve) => {
    post(`crop-history/${id}`, data).then((response) => {
      console.log('updateCropEvent', id, data, 'response', response)
      resolve(response)
    })
  })
}

// plots
export const fetchPlots = () => {
  return new Promise((resolve) => {
    get('plots').then((response) => {
      console.log('fetchPlots', 'response', response)
      resolve(response)
    })
  })
}
export const fetchPlot = (id) => {
  return new Promise((resolve) => {
    get(`plots/${id}`).then((response) => {
      console.log('fetchPlot', id, 'response', response)
      resolve(response)
    })
  })
}
export const createPlot = (data) => {
  return new Promise((resolve) => {
    post('plots', data).then((response) => {
      console.log('createPlot', data, 'response', response)
      resolve(response)
    })
  })
}
export const updatePlot = (id, data) => {
  return new Promise((resolve) => {
    post(`plots/${id}`, data).then((response) => {
      console.log('updatePlot', id, data, 'response', response)
      resolve(response)
    })
  })
}
export const deletePlot = (id) => {
  return new Promise((resolve) => {
    destroy(`plots/${id}`).then((response) => {
      console.log('deletePlot', id, 'response', response)
      resolve(response)
    })
  })
}
