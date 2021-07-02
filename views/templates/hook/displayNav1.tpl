<div class="geoweather">
    <div class="condition">
        <img src="{$condition.icon}" alt="condition">
        <span>{$condition.text}</span>
    </div>
    <div class="weather">
        <span><b>{l s='Temperature: ' d='Modules.Jnfgeoweather.Jnfgeoweather'}</b>{$temp}</span>
        <span> - </span>
        <span><b>{l s='humidity: ' d='Modules.Jnfgeoweather.Jnfgeoweather'}</b>{$humidity}</span>
    </div>
    <div class="location">
        <b>{$location}</b>
    </div>
</div>
