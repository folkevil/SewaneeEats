!function (a) {
  "function" == typeof define && define.amd ? define(["jquery", "moment"], a) : "object" == typeof exports ? module.exports = a(require("jquery"), require("moment")) : a(jQuery, moment)
}(function (a, b) {
  !function () {
    "use strict";
    var a = {
      words: {
        m: ["jedan minut", "jedne minute"],
        mm: ["minut", "minute", "minuta"],
        h: ["jedan sat", "jednog sata"],
        hh: ["sat", "sata", "sati"],
        dd: ["dan", "dana", "dana"],
        MM: ["mesec", "meseca", "meseci"],
        yy: ["godina", "godine", "godina"]
      }, correctGrammaticalCase: function (a, b) {
        return 1 === a ? b[0] : a >= 2 && 4 >= a ? b[1] : b[2]
      }, translate: function (b, c, d) {
        var e = a.words[d];
        return 1 === d.length ? c ? e[0] : e[1] : b + " " + a.correctGrammaticalCase(b, e)
      }
    }, c = (b.defineLocale || b.lang).call(b, "sr", {
      months: "januar_februar_mart_april_maj_jun_jul_avgust_septembar_oktobar_novembar_decembar".split("_"),
      monthsShort: "jan._feb._mar._apr._maj_jun_jul_avg._sep._okt._nov._dec.".split("_"),
      monthsParseExact: !0,
      weekdays: "nedelja_ponedeljak_utorak_sreda_četvrtak_petak_subota".split("_"),
      weekdaysShort: "ned._pon._uto._sre._čet._pet._sub.".split("_"),
      weekdaysMin: "ne_po_ut_sr_če_pe_su".split("_"),
      weekdaysParseExact: !0,
      longDateFormat: {
        LT: "H:mm",
        LTS: "H:mm:ss",
        L: "DD. MM. YYYY",
        LL: "D. MMMM YYYY",
        LLL: "D. MMMM YYYY H:mm",
        LLLL: "dddd, D. MMMM YYYY H:mm"
      },
      calendar: {
        sameDay: "[danas u] LT", nextDay: "[sutra u] LT", nextWeek: function () {
          switch (this.day()) {
            case 0:
              return "[u] [nedelju] [u] LT";
            case 3:
              return "[u] [sredu] [u] LT";
            case 6:
              return "[u] [subotu] [u] LT";
            case 1:
            case 2:
            case 4:
            case 5:
              return "[u] dddd [u] LT"
          }
        }, lastDay: "[juče u] LT", lastWeek: function () {
          var a = ["[prošle] [nedelje] [u] LT", "[prošlog] [ponedeljka] [u] LT", "[prošlog] [utorka] [u] LT", "[prošle] [srede] [u] LT", "[prošlog] [četvrtka] [u] LT", "[prošlog] [petka] [u] LT", "[prošle] [subote] [u] LT"];
          return a[this.day()]
        }, sameElse: "L"
      },
      relativeTime: {
        future: "za %s",
        past: "pre %s",
        s: "nekoliko sekundi",
        m: a.translate,
        mm: a.translate,
        h: a.translate,
        hh: a.translate,
        d: "dan",
        dd: a.translate,
        M: "mesec",
        MM: a.translate,
        y: "godinu",
        yy: a.translate
      },
      ordinalParse: /\d{1,2}\./,
      ordinal: "%d.",
      week: {dow: 1, doy: 7}
    });
    return c
  }(), a.fullCalendar.datepickerLang("sr", "sr", {
    closeText: "Затвори",
    prevText: "&#x3C;",
    nextText: "&#x3E;",
    currentText: "Данас",
    monthNames: ["Јануар", "Фебруар", "Март", "Април", "Мај", "Јун", "Јул", "Август", "Септембар", "Октобар", "Новембар", "Децембар"],
    monthNamesShort: ["Јан", "Феб", "Мар", "Апр", "Мај", "Јун", "Јул", "Авг", "Сеп", "Окт", "Нов", "Дец"],
    dayNames: ["Недеља", "Понедељак", "Уторак", "Среда", "Четвртак", "Петак", "Субота"],
    dayNamesShort: ["Нед", "Пон", "Уто", "Сре", "Чет", "Пет", "Суб"],
    dayNamesMin: ["Не", "По", "Ут", "Ср", "Че", "Пе", "Су"],
    weekHeader: "Сед",
    dateFormat: "dd.mm.yy",
    firstDay: 1,
    isRTL: !1,
    showMonthAfterYear: !1,
    yearSuffix: ""
  }), a.fullCalendar.lang("sr", {
    buttonText: {month: "Месец", week: "Недеља", day: "Дан", list: "Планер"},
    allDayText: "Цео дан",
    eventLimitText: function (a) {
      return "+ још " + a
    }
  })
});