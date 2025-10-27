// src/App.js
import React, { useState, useEffect } from "react";
import "./App.css"; // Styling file

function App() {
  const [weatherData, setWeatherData] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("http://127.0.0.1:8000/api/weather")
      .then(res => res.json())
      .then(data => {
        setWeatherData(data); // store data from backend
        setLoading(false);
      })
      .catch(err => {
        console.error("Error fetching weather:", err);
        setLoading(false);
      });
  }, []);

  if (loading) return <p>Loading weather data...</p>;

  return (
    <div className="app">
      <h1>🌤️ Weather Dashboard</h1>

      <div className="weather-cards">
        {weatherData.map(item => {
          const main = item.data?.main || {};
          const weather = item.data?.weather?.[0] || {};
          const wind = item.data?.wind || {};

          return (
            <div key={item.city} className="card">
              <h2>{item.city}</h2>
              {weather.icon && (
                <img
                  src={`http://openweathermap.org/img/wn/${weather.icon}@2x.png`}
                  alt={weather.description || ""}
                />
              )}
              <p>Temperature: {main.temp ?? "N/A"}°C</p>
              <p>Feels like: {main.feels_like ?? "N/A"}°C</p>
              <p>Condition: {weather.description || "N/A"}</p>
              <p>Humidity: {main.humidity ?? "N/A"}%</p>
              <p>Wind: {wind.speed ?? "N/A"} m/s</p>
            </div>
          );
        })}
      </div>
    </div>
  );
}

export default App;
