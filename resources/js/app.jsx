import React from 'react'
import { createRoot } from 'react-dom/client'
import Forecast from './components/Forecast'

function App() {
  return (
      <div className="max-w-[920px] mx-auto my-10 px-4">
          <h1 className="mb-2 text-3xl font-semibold">5-Day Weather Forecast</h1>
          <p className="mt-0 text-gray-600">
              Select a city to load forecast (Brisbane, Gold Coast, Sunshine Coast).
          </p>
          <Forecast />
      </div>

  )
}

const root = createRoot(document.getElementById('root'))
root.render(<App />)
