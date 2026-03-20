import type { WeatherRow, UnitSystem } from "../types";
import { formatDate, getAverageTemp, getMedian, getMode, celsiusToFahrenheit,
    getPrecipitationUnitLabel, getTemperatureUnitLabel, millimeterToInch } from "../utils/weatherStats";

type WeatherTableProps = {
    rows: WeatherRow[];
    unitSystem: UnitSystem;
};

function formatValue(value: number | number[] | null, unit: string): string {
    if (value === null) {
        return "nincs";
    }

    if (Array.isArray(value)) {
        return value.map((item) => `${item.toFixed(2)} ${unit}`).join(" és ");
    }

    return `${value.toFixed(2)} ${unit}`;
}

function convertTemperature(value: number, unitSystem: UnitSystem): number {
    return unitSystem === "si" ? value : celsiusToFahrenheit(value);
}

function convertPrecipitation(value: number, unitSystem: UnitSystem): number {
    return unitSystem === "si" ? value : millimeterToInch(value);
}

export function WeatherTable({ rows, unitSystem }: WeatherTableProps) {
    const temperatureUnit = getTemperatureUnitLabel(unitSystem);
    const precipitationUnit = getPrecipitationUnitLabel(unitSystem);

    const minTempValues = rows.map((row) => convertTemperature(row.minTemp, unitSystem));
    const maxTempValues = rows.map((row) => convertTemperature(row.maxTemp, unitSystem));
    const averageTempValues = rows.map((row) =>
        convertTemperature(getAverageTemp(row.minTemp, row.maxTemp), unitSystem),
    );
    const precipitationValues = rows.map((row) =>
        convertPrecipitation(row.precipitation, unitSystem),
    );

    return (
        <div className="overflow-x-auto">
        <table className="w-full border-collapse rounded-lg bg-white">
            <thead>
            <tr className="border-b border-slate-300 bg-slate-100 text-left">
                <th className="px-4 py-3">Dátum</th>
                <th className="px-4 py-3">Minimum hőmérséklet</th>
                <th className="px-4 py-3">Maximum hőmérséklet</th>
                <th className="px-4 py-3">Átlaghőmérséklet</th>
                <th className="px-4 py-3">Csapadék</th>
            </tr>
            </thead>

            <tbody>
            {rows.map((row) => {
                const minTemp = convertTemperature(row.minTemp, unitSystem);
                const maxTemp = convertTemperature(row.maxTemp, unitSystem);
                const averageTemp = convertTemperature(
                getAverageTemp(row.minTemp, row.maxTemp),
                unitSystem,
                );
                const precipitation = convertPrecipitation(row.precipitation, unitSystem);

                return (
                <tr key={row.date} className="border-b border-slate-200">
                    <td className="px-4 py-3">{formatDate(row.date)}</td>
                    <td className="px-4 py-3">{minTemp.toFixed(2)} {temperatureUnit}</td>
                    <td className="px-4 py-3">{maxTemp.toFixed(2)} {temperatureUnit}</td>
                    <td className="px-4 py-3">{averageTemp.toFixed(2)} {temperatureUnit}</td>
                    <td className="px-4 py-3">{precipitation.toFixed(2)} {precipitationUnit}</td>
                </tr>
                );
            })}
            </tbody>

            <tfoot>
            <tr className="border-b border-slate-200 bg-slate-50 font-semibold">
                <td className="px-4 py-3">Medián</td>
                <td className="px-4 py-3">{formatValue(getMedian(minTempValues), temperatureUnit)}</td>
                <td className="px-4 py-3">{formatValue(getMedian(maxTempValues), temperatureUnit)}</td>
                <td className="px-4 py-3">{formatValue(getMedian(averageTempValues), temperatureUnit)}</td>
                <td className="px-4 py-3">{formatValue(getMedian(precipitationValues), precipitationUnit)}</td>
            </tr>
            <tr className="bg-slate-50 font-semibold">
                <td className="px-4 py-3">Módusz</td>
                <td className="px-4 py-3">{formatValue(getMode(minTempValues), temperatureUnit)}</td>
                <td className="px-4 py-3">{formatValue(getMode(maxTempValues), temperatureUnit)}</td>
                <td className="px-4 py-3">{formatValue(getMode(averageTempValues), temperatureUnit)}</td>
                <td className="px-4 py-3">{formatValue(getMode(precipitationValues), precipitationUnit)}</td>
            </tr>
            </tfoot>
        </table>
        </div>
    );
}