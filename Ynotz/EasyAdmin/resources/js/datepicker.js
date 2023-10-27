export default () => {
    return {
        MONTH_NAMES: [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        ],
        MONTH_SHORT_NAMES: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ],
        DAYS: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        showDatepicker: false,
        datepickerValue: "",
        selectedDate: "",
        dateFormat: "DD-MM-YYYY",
        startYear: 2000,
        endYear: 2050,
        yearsRange: [],
        month: "",
        year: "",
        no_of_days: [],
        blankdays: [],
        fireInputEvent: false,
        listeners: {},
        resetSources: [],
        toggleListeners: {},
        showelement: true,
        displaybox: null,
        position: 'bottom',
        initDate() {
            let today;
            if (this.selectedDate) {
                today = new Date(Date.parse(this.selectedDate));
            } else {
                today = new Date();
            }
            this.month = today.getMonth();
            this.year = today.getFullYear();
            if (this.selectedDate) {
                this.datepickerValue = this.formatDateForDisplay(
                    today
                );
            } else {
                this.datepickerValue = '';
            }
            for (i = this.startYear; i <= this.endYear; i++) {
                this.yearsRange.push(i);
            }
        },
        formatDateForDisplay(date) {
            let formattedDay = this.DAYS[date.getDay()];
            let formattedDate = ("0" + date.getDate()).slice(
                -2
            ); // appends 0 (zero) in single digit date
            let formattedMonth = this.MONTH_NAMES[date.getMonth()];
            let formattedMonthShortName =
                this.MONTH_SHORT_NAMES[date.getMonth()];
            let formattedMonthInNumber = (
                "0" +
                (parseInt(date.getMonth()) + 1)
            ).slice(-2);
            let formattedYear = date.getFullYear();
            if (this.dateFormat === "DD-MM-YYYY") {
                return `${formattedDate}-${formattedMonthInNumber}-${formattedYear}`; // 02-04-2021
            }
            if (this.dateFormat === "YYYY-MM-DD") {
                return `${formattedYear}-${formattedMonthInNumber}-${formattedDate}`; // 2021-04-02
            }
            if (this.dateFormat === "D d M, Y") {
                return `${formattedDay} ${formattedDate} ${formattedMonthShortName} ${formattedYear}`; // Tue 02 Mar 2021
            }
            return `${formattedDay} ${formattedDate} ${formattedMonth} ${formattedYear}`;
        },
        isSelectedDate(date) {
            const d = new Date(this.year, this.month, date);
            return this.datepickerValue ===
                this.formatDateForDisplay(d) ?
                true :
                false;
        },
        isToday(date) {
            const today = new Date();
            const d = new Date(this.year, this.month, date);
            return today.toDateString() === d.toDateString() ?
                true :
                false;
        },
        getDateValue(date) {
            let selectedDate = new Date(
                this.year,
                this.month,
                date
            );
            this.datepickerValue = this.formatDateForDisplay(
                selectedDate
            );
            let m = String(this.month + 1);
            m = m.lenght == 1 ? '0' + m : m;
            let d = String(date);
            d = d.lenght == 1 ? '0' + d : d;
            this.selectedDate = this.year + '-' + m + '-' + d;
            if (this.fireInputEvent) {
                $dispatch('eaforminputevent', { source: '{{$name}}', value: this.datepickerValue });
            }
            // this.$refs.date.value = selectedDate.getFullYear() + "-" + ('0' + formattedMonthInNumber).slice(-2) + "-" + ('0' + selectedDate.getDate()).slice(-2);
            this.isSelectedDate(date);
            this.showDatepicker = false;
        },
        getNoOfDays() {
            let daysInMonth = new Date(
                this.year,
                this.month + 1,
                0
            ).getDate();
            // find where to start calendar day of week
            let dayOfWeek = new Date(
                this.year,
                this.month
            ).getDay();
            let blankdaysArray = [];
            for (var i = 1; i <= dayOfWeek; i++) {
                blankdaysArray.push(i);
            }
            let daysArray = [];
            for (var i = 1; i <= daysInMonth; i++) {
                daysArray.push(i);
            }
            this.blankdays = blankdaysArray;
            this.no_of_days = daysArray;
        },
        updateOnEvent(source, value) {
            if (Object.keys(this.listeners).includes(source)) {
                if (this.listeners[source].serviceclass == null) {
                    this.textval = '';
                } else {
                    let url = '{{route('easyadmin.fetch', ['service' => '__service__', 'method' => '__method__'])}}';
        url = url.replace('__service__', this.listeners[source].serviceclass);
        url = url.replace('__method__', this.listeners[source].method);
        axios.get(
            url,
            {
                params: { 'value': value }
            }
        ).then((r) => {
            let data = JSON.parse(r.results);
            this.month = data.month;
            this.year = data.year;
            this.selectedDate = r.date;
        }).catch((e) => {
            console.log(e);
        });
    }
}
    },
toggleOnEvent(source, value) {
    if (Object.keys(this.toggleListeners).includes(source)) {
        this.toggleListeners[source].forEach((item) => {
            switch (item.condition) {
                case '==':
                    if (item.value == value) {
                        this.showelement = item.show;
                    }
                    break;
                case '!=':
                    if (item.value != value) {
                        this.showelement = item.show;
                    }
                    break;
                case '>':
                    if (item.value > value) {
                        this.showelement = item.show;
                    }
                    break;
                case '<':
                    if (item.value < value) {
                        this.showelement = item.show;
                    }
                    break;
                case '>=':
                    if (item.value >= value) {
                        this.showelement = item.show;
                    }
                    break;
                case '<=':
                    if (item.value <= value) {
                        this.showelement = item.show;
                    }
                    break;
            }
        });
    }
},
resetOnEvent(detail) {
    if (this.resetSources.includes(detail.source)) {
        this.reset();
    }
},
reset() {
    this.selectedDate = '';
    this.datepickerValue = '';
    this.errors = '';
},
setCalendarMonth(m) {
    this.month = m;
    this.getNoOfDays();
},
setCalendarYear(y) {
    this.year = y;
    this.getNoOfDays();
},
calendarFromDate() {
    let date;

    if (this.selectedDate) {
        date = new Date(Date.parse(this.selectedDate));
    } else {
        date = new Date();
    }
    this.month = date.getMonth();
    this.year = date.getFullYear();
    this.getNoOfDays();
},
setPosition() {
    let x = window.innerHeight - this.displaybox.getBoundingClientRect().bottom;
    this.position = x < 200 ? 'top' : 'bottom';
}
}
}
