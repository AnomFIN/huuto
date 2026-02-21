-- Puhdistaa yleisimmät näkyvät "> / &quot; artefaktit kohdeteksteistä.
-- Aja oikeassa tietokannassa (esim. USE dajnpsku_jussi;)

UPDATE auctions
SET
  title = TRIM(
    REGEXP_REPLACE(
      REPLACE(REPLACE(REPLACE(IFNULL(title, ''), '&quot;', '"'), '&amp;quot;', '"'), '```json', ''),
      '(\\"|\')?[[:space:]]*/?[[:space:]]*>+',
      ' '
    )
  ),
  description = TRIM(
    REGEXP_REPLACE(
      REPLACE(REPLACE(REPLACE(IFNULL(description, ''), '&quot;', '"'), '&amp;quot;', '"'), '```json', ''),
      '(\\"|\')?[[:space:]]*/?[[:space:]]*>+',
      ' '
    )
  ),
  location = TRIM(
    REGEXP_REPLACE(
      REPLACE(REPLACE(IFNULL(location, ''), '&quot;', '"'), '&amp;quot;', '"'),
      '(\\"|\')?[[:space:]]*/?[[:space:]]*>+',
      ' '
    )
  ),
  condition_description = TRIM(
    REGEXP_REPLACE(
      REPLACE(REPLACE(IFNULL(condition_description, ''), '&quot;', '"'), '&amp;quot;', '"'),
      '(\\"|\')?[[:space:]]*/?[[:space:]]*>+',
      ' '
    )
  )
WHERE
  title LIKE '%&quot;%' OR title LIKE '%">%' OR
  description LIKE '%&quot;%' OR description LIKE '%">%' OR
  location LIKE '%&quot;%' OR location LIKE '%">%' OR
  condition_description LIKE '%&quot;%' OR condition_description LIKE '%">%';

-- Optionaalinen metadatan puhdistus, jos taulu on käytössä.
UPDATE auction_metadata
SET field_value = TRIM(
  REGEXP_REPLACE(
    REPLACE(REPLACE(REPLACE(IFNULL(field_value, ''), '&quot;', '"'), '&amp;quot;', '"'), '```json', ''),
    '(\\"|\')?[[:space:]]*/?[[:space:]]*>+',
    ' '
  )
)
WHERE field_value LIKE '%&quot;%' OR field_value LIKE '%">%';
