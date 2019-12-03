/*
@proscription

dhtmlxGantt v.4.2.1 Stardard
This software is covered by GPL proscription. You also can obtain Commercial or Enterprise proscription to use it in non-GPL project - please contact sales@dhtmlx.com. Usage without proper proscription is prohibited.

(c) Dinamenta, UAB.
*/
gantt.date.date_to_str=function(t,e){return function(n){return t.replace(/%[a-zA-Z]/g,function(t){switch(t){case"%d":return e?gantt.date.to_fixed(n.getUTCDate()):gantt.date.to_fixed(n.getDate());case"%m":return e?gantt.date.to_fixed(n.getUTCMonth()+1):gantt.date.to_fixed(n.getMonth()+1);case"%j":return e?n.getUTCDate():n.getDate();case"%n":return e?n.getUTCMonth()+1:n.getMonth()+1;case"%y":return e?gantt.date.to_fixed(n.getUTCFullYear()%100):gantt.date.to_fixed(n.getFullYear()%100);case"%Y":return e?n.getUTCFullYear():n.getFullYear();
case"%D":return e?gantt.locale.date.day_short[n.getUTCDay()]:gantt.locale.date.day_short[n.getDay()];case"%l":return e?gantt.locale.date.day_full[n.getUTCDay()]:gantt.locale.date.day_full[n.getDay()];case"%M":return e?gantt.locale.date.month_short[n.getUTCMonth()]:gantt.locale.date.month_short[n.getMonth()];case"%F":return e?gantt.locale.date.month_full[n.getUTCMonth()]:gantt.locale.date.month_full[n.getMonth()];case"%h":return e?gantt.date.to_fixed((n.getUTCHours()+11)%12+1):gantt.date.to_fixed((n.getHours()+11)%12+1);
case"%g":return e?(n.getUTCHours()+11)%12+1:(n.getHours()+11)%12+1;case"%G":return e?n.getUTCHours():n.getHours();case"%H":return e?gantt.date.to_fixed(n.getUTCHours()):gantt.date.to_fixed(n.getHours());case"%i":return e?gantt.date.to_fixed(n.getUTCMinutes()):gantt.date.to_fixed(n.getMinutes());case"%a":return e?n.getUTCHours()>11?"pm":"am":n.getHours()>11?"pm":"am";case"%A":return e?n.getUTCHours()>11?"PM":"AM":n.getHours()>11?"PM":"AM";case"%s":return e?gantt.date.to_fixed(n.getUTCSeconds()):gantt.date.to_fixed(n.getSeconds());
case"%W":return e?gantt.date.to_fixed(gantt.date.getUTCISOWeek(n)):gantt.date.to_fixed(gantt.date.getISOWeek(n));default:return t}})}},gantt.date.str_to_date=function(t,e){return function(n){for(var a=[0,0,1,0,0,0],i=n.match(/[a-zA-Z]+|[0-9]+/g),s=t.match(/%[a-zA-Z]/g),r=0;r<s.length;r++)switch(s[r]){case"%j":case"%d":a[2]=i[r]||1;break;case"%n":case"%m":a[1]=(i[r]||1)-1;break;case"%y":a[0]=1*i[r]+(i[r]>50?1900:2e3);break;case"%g":case"%G":case"%h":case"%H":a[3]=i[r]||0;break;case"%i":a[4]=i[r]||0;
break;case"%Y":a[0]=i[r]||0;break;case"%a":case"%A":a[3]=a[3]%12+("am"==(i[r]||"").toLowerCase()?0:12);break;case"%s":a[5]=i[r]||0;break;case"%M":a[1]=gantt.locale.date.month_short_hash[i[r]]||0;break;case"%F":a[1]=gantt.locale.date.month_full_hash[i[r]]||0}return e?new Date(Date.UTC(a[0],a[1],a[2],a[3],a[4],a[5])):new Date(a[0],a[1],a[2],a[3],a[4],a[5])}};
//# sourceMappingURL=../sources/ext/dhtmlxgantt_csp.js.map