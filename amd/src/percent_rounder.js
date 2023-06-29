/**
 * Makes sure rounded percentages add up to exactly 100.
 */
export default class PercentRounder {
    constructor() {
        this.reset();
    }

    /**
     * This resets internal values and should be called before reusing an instance.
     */
    reset() {
        this.cumulatedValue = 0;
        this.roundingBaseline = 0;
    }

    /**
     * Rounds the provided number incorporating previously rounded values.
     *
     * @param {number} percentage - A number smaller or equal to 100.
     * @return {number} The given number rounded to the next integer, which depends on previously rounded numbers.
     */
    round(percentage) {
        this.cumulatedValue += percentage;
        const roundedCumulatedValue = Math.round(this.cumulatedValue);
        const roundedPercentage = roundedCumulatedValue - this.roundingBaseline;
        this.roundingBaseline = roundedCumulatedValue;
        return roundedPercentage;
    }
}
