// src/App.js
import React, { useState, useEffect } from "react";
import "./App.css"; // Styling file

function App() {
  // ✅ State to store weather data
  const [weatherData, setWeatherData] = useState([]);
  
  // ✅ State to know if data is loading
  const [loading, setLoading] = useState(true);

  // ✅ Fetch weather data from backend when component mounts
  useEffect(() => {
    fetch("http://127.0.0.1:8000/api/weather")
      .then(res => res.json())
      .then(data => {
        setWeatherData(data); // store data in state
        setLoading(false);    // set loading to false
      })
      .catch(err => {
        console.error("Error fetching weather:", err);
        setLoading(false);
      });
  }, []);

  // ✅ Show loading message while data is being fetched
  if (loading) return <p>Loading weather data...</p>;

  return (
    <div className="app">
      <h1>🌤️ Weather Dashboard</h1>
      
      <div className="weather-cards">
        {weatherData.map(item => (
          <div key={item.city} className="card">
            <h2>{item.city}</h2>
            <img
              src={`http://openweathermap.org/img/wn/${item.data.weather[0].icon}@2x.png`}
              alt={item.data.weather[0].description}
            />
            <p>Temperature: {item.data.main.temp}°C</p>
            <p>Feels like: {item.data.main.feels_like}°C</p>
            <p>Condition: {item.data.weather[0].description}</p>
            <p>Humidity: {item.data.main.humidity}%</p>
            <p>Wind: {item.data.wind.speed} m/s</p>
          </div>
        ))}
      </div>
    </div>
  );
}

export default App;
