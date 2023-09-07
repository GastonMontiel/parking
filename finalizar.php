// si existe
spaceId
vehicleId

// si es nuevo
spaceId
licensePlate
colorId
brandId
modelId

// logica:
-- select de space para validar que exista y está vacio
SELECT
s.id
FROM spaces s
LEFT JOIN vehicles v
ON s.id = v.spaceId
WHERE s.id = ? AND v.spaceId IS NULL

// si tengo vehicleId (existe):
-- select de vehicle para validar que exista y no esté en un lugar
SELECT id FROM vehicles WHERE id = ? AND spaceId IS NULL
UPDATE vehicles SET spaceId = ? WHERE id = ?

// si no :
-- select para validar que la matricula no esté ingresada y los otros datos sean validos
SELECT
(SELECT id FROM vehicles WHERE licensePlate = ?) AS vehicle,
(SELECT id FROM colors WHERE id = ?) AS color,
(SELECT id FROM brands WHERE id = ?) AS brand,
(SELECT id FROM models WHERE id = ?) AS model
INSERT INTO vehicles (licensePlate, brandId, colorId, modelId, spaceId) VALUES (?, ?, ?, ?, ?)

// otras cosas
-- para vaciar un espacio es:
UPDATE vehicles SET spaceId = NULL WHERE id = ?