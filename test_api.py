import requests

BASE_URL = 'http://localhost:8080/notas'
nota_id_creada = None

def test_get_notas():
    response = requests.get(BASE_URL)
    print("Respuesta GET:", response.text)

    assert response.status_code == 200, "❌ Código de estado inesperado"
    
    try:
        data = response.json()
    except Exception as e:
        print("❌ Error al convertir respuesta a JSON:", response.text)
        raise e

    assert isinstance(data, list), "❌ La respuesta no es una lista"
    print("✅ test_get_notas pasó")

def test_post_nota():
    global nota_id_creada
    payload = {"tipo": "nota", "mensaje": "PRUEBA AUTOMÁTICA"}
    response = requests.post(BASE_URL, json=payload)
    print("Respuesta POST:", response.text)

    assert response.status_code == 200, "❌ POST falló"

    data = response.json()
    nota_id_creada = data['id']
    assert data['mensaje'] == payload['mensaje'], "❌ El mensaje no coincide"
    print("✅ test_post_nota pasó")

def test_put_nota():
    payload = {"mensaje": "PRUEBA MODIFICADA"}
    response = requests.put(f"{BASE_URL}/{nota_id_creada}", json=payload)
    print("Respuesta PUT:", response.text)

    assert response.status_code == 200, "❌ PUT falló"
    assert "actualizada" in response.text.lower(), "❌ PUT no devolvió mensaje esperado"
    print("✅ test_put_nota pasó")

def test_delete_nota():
    response = requests.delete(f"{BASE_URL}/{nota_id_creada}")
    print("Respuesta DELETE:", response.text)

    assert response.status_code == 200, "❌ DELETE falló"
    assert "eliminada" in response.text.lower(), "❌ DELETE no devolvió mensaje esperado"
    print("✅ test_delete_nota pasó")

# 🔁 Ejecutar pruebas
test_get_notas()
test_post_nota()
test_put_nota()
test_delete_nota()
