export type City = {
  id: number;
  name: string;
  region: string;
};

export type Region = {
  name: string;
};

export type WeatherRow = {
  date: string;
  cityId?: number;
  cityName?: string;
  region?: string;
  minTemp: number;
  maxTemp: number;
  precipitation: number;
};

export type UnitSystem = "si" | "imperial";