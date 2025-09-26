
# MIGRATION na alpha.18
Pridané / zjednodušené polia:
- observer_name (text, default LuBenea)
- source_text (text)
- habitat (text)
- (lat/lng sú už v DB, len sa teraz viac používajú a dopĺňajú z mapy)

## Jednorazovo:
1) Zálohuj `data/app.db`.
2) Spusť `migrate_alpha18.php` a potom súbor zmaž.
