export function getAverageTemp(minTemp: number, maxTemp: number): number {
    return (minTemp + maxTemp) / 2;
}

export function getMedian(values: number[]): number | null {
    if (values.length === 0) {
        return null;
    }

    const sortedValues = [...values].sort((leftValue, rightValue) => leftValue - rightValue);
    const middleIndex = Math.floor(sortedValues.length / 2);

    if (sortedValues.length % 2 === 0) {
        return (sortedValues[middleIndex - 1] + sortedValues[middleIndex]) / 2;
    }

    return sortedValues[middleIndex];
}

export function getMode(values: number[]): number[] | null {
    if (values.length === 0) {
        return null;
    }

    const counts = new Map<number, number>();

    for (const value of values) {
        counts.set(value, (counts.get(value) ?? 0) + 1);
    }

    let highestCount = 0;

    for (const count of counts.values()) {
        if (count > highestCount) {
        highestCount = count;
        }
    }

    if (highestCount <= 1) {
        return null;
    }

    const modes: number[] = [];

    for (const [value, count] of counts.entries()) {
        if (count === highestCount) {
        modes.push(value);
        }
    }

    return modes.sort((leftValue, rightValue) => leftValue - rightValue);
}

export function formatDate(date: string): string {
    if (date.length !== 8) {
        return date;
    }
    
    const year = date.slice(0, 4);
    const month = date.slice(4, 6);
    const day = date.slice(6, 8);

    return `${year}-${month}-${day}`;
}

export function celsiusToFahrenheit(value: number): number {
    return (value * 9) / 5 + 32;
}

export function millimeterToInch(value: number): number {
    return value / 25.4;
}

export function getTemperatureUnitLabel(unitSystem: "si" | "imperial"): string {
    return unitSystem === "si" ? "°C" : "°F";
}

export function getPrecipitationUnitLabel(unitSystem: "si" | "imperial"): string {
    return unitSystem === "si" ? "mm" : "inch";
}