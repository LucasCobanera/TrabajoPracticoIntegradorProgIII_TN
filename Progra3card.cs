using System;
using MySqlConnection = MySql.Data.MySqlClient.MySqlConnection;
using MySqlCommand = MySql.Data.MySqlClient.MySqlCommand;
using MySqlDataReader = MySql.Data.MySqlClient.MySqlDataReader;

namespace Progra3Card.Administrativo
{
    class Program
    {
        private static string connectionString = "Server=localhost;Database=mi_banco_db;Uid=root;Pwd=root;";

        static void Main(string[] args)
        {
            bool salir = false;
            while (!salir)
            {
                Console.Clear();
                Console.WriteLine("========================================");
                Console.WriteLine("    SISTEMA ADMINISTRATIVO PROGRA3CARD   ");
                Console.WriteLine("========================================");
                Console.WriteLine("1. Emitir Nueva Tarjeta (Alta de Cliente)");
                Console.WriteLine("2. Listar Tarjetas");
                Console.WriteLine("3. Ver Detalle de una Tarjeta / Cliente");
                Console.WriteLine("4. Eliminar Tarjeta (Baja de Sistema)");
                Console.WriteLine("5. Emitir Nueva Liquidación Mensual");
                Console.WriteLine("6. Salir");
                Console.WriteLine("========================================");
                Console.Write("Seleccione una opción: ");

                string opcion = Console.ReadLine();

                switch (opcion)
                {
                    case "1": MenuEmitirTarjeta(); break;
                    case "2": MenuListarTarjetas(); break;
                    case "3": MenuVerDetalleTarjeta(); break;
                    case "4": MenuEliminarTarjeta(); break;
                    case "5": MenuEmitirLiquidacion(); break;
                    case "6": salir = true; break;
                    default:
                        Console.WriteLine("Opción no válida. Presione una tecla para continuar...");
                        Console.ReadKey();
                        break;
                }
            }
        }

        static void MenuEmitirTarjeta()
        {
            Console.Clear();
            Console.WriteLine("--- EMITIR NUEVA TARJETA (ALTA DE CLIENTE) ---");
            
            Console.Write("Ingrese Documento del Cliente: ");
            string documento = Console.ReadLine();

            Console.WriteLine("Seleccione Tipo de Documento:");
            Console.WriteLine("1. DNI");
            Console.WriteLine("2. PASAPORTE");
            Console.Write("Seleccione una opción (1 o 2): ");
            string opDoc = Console.ReadLine();
            string tipoDoc = "DNI";
            if (opDoc == "2")
            {
                tipoDoc = "PASAPORTE";
            }

            Console.Write("Ingrese Nombre del Cliente: ");
            string nombre = Console.ReadLine();
            Console.Write("Ingrese Apellido del Cliente: ");
            string apellido = Console.ReadLine();
            Console.Write("Ingrese Fecha de Nacimiento (YYYY-MM-DD): ");
            string fechaNac = Console.ReadLine();
            Console.Write("Ingrese Correo Electrónico: ");
            string email = Console.ReadLine();

            Console.Write("Ingrese Número de Tarjeta (16 dígitos): ");
            string numTarjeta = Console.ReadLine();

            Console.WriteLine("Seleccione el Banco Emisor:");
            Console.WriteLine("1. Banco Nación");
            Console.WriteLine("2. Banco Provincia");
            Console.WriteLine("3. Banco Galicia");
            Console.WriteLine("4. Banco Santander");
            Console.WriteLine("5. Banco BBVA");
            Console.WriteLine("6. Banco Macro");
            Console.Write("Seleccione una opción (1-6): ");
            string opBanco = Console.ReadLine();
            string bancoEmisor = "Banco Nación";
            if (opBanco == "2") bancoEmisor = "Banco Provincia";
            else if (opBanco == "3") bancoEmisor = "Banco Galicia";
            else if (opBanco == "4") bancoEmisor = "Banco Santander";
            else if (opBanco == "5") bancoEmisor = "Banco BBVA";
            else if (opBanco == "6") bancoEmisor = "Banco Macro";

            using (MySqlConnection conexion = new MySqlConnection(connectionString))
            {
                try
                {
                    conexion.Open();

                    //verificacion si el cliente ya existe
                    bool usuarioExiste = false;
                    string consultaChq = "SELECT 1 FROM usuarios WHERE documento = @documento LIMIT 1";
                    using (MySqlCommand chqConsulta = new MySqlCommand(consultaChq, conexion))
                    {
                        chqConsulta.Parameters.AddWithValue("@documento", documento);
                        using (MySqlDataReader lector = chqConsulta.ExecuteReader())
                        {
                            if (lector.HasRows)
                            {
                                usuarioExiste = true;
                            }
                        }
                    }

                    // sino existe, lo insertamos
                    if (!usuarioExiste)
                    {
                        string consultaInsertarUsuario = "INSERT INTO usuarios (documento, tipo_doc, nombre, apellido, fecha_nacimiento, email, usuario, password) " +
                                            "VALUES (@documento, @tipo_doc, @nombre, @apellido, @fecha_nac, @email, NULL, NULL)";
                        using (MySqlCommand comandoUsuario = new MySqlCommand(consultaInsertarUsuario, conexion))
                        {
                            comandoUsuario.Parameters.AddWithValue("@documento", documento);
                            comandoUsuario.Parameters.AddWithValue("@tipo_doc", tipoDoc);
                            comandoUsuario.Parameters.AddWithValue("@nombre", nombre);
                            comandoUsuario.Parameters.AddWithValue("@apellido", apellido);
                            comandoUsuario.Parameters.AddWithValue("@fecha_nac", fechaNac);
                            comandoUsuario.Parameters.AddWithValue("@email", email);
                            comandoUsuario.ExecuteNonQuery();
                        }
                    }

                    //insertar la tarjeta 
                    string consultaInsertarTarjeta = "INSERT INTO tarjetas (numero_tarjeta, banco_emisor, estado, saldo, dni_titular) " +
                                        "VALUES (@num_tarjeta, @banco_emisor, 'Activa', 0.00, @dni_titular)";
                    using (MySqlCommand comandoTarjeta = new MySqlCommand(consultaInsertarTarjeta, conexion))
                    {
                        comandoTarjeta.Parameters.AddWithValue("@num_tarjeta", numTarjeta);
                        comandoTarjeta.Parameters.AddWithValue("@banco_emisor", bancoEmisor);
                        comandoTarjeta.Parameters.AddWithValue("@dni_titular", documento);
                        
                        int filas = comandoTarjeta.ExecuteNonQuery();
                        if (filas > 0)
                        {
                            Console.ForegroundColor = ConsoleColor.Green;
                            Console.WriteLine("\n¡Cliente y tarjeta registrados exitosamente!");
                            Console.ResetColor();
                        }
                    }
                }
                catch (Exception ex)
                {
                    Console.ForegroundColor = ConsoleColor.Red;
                    Console.WriteLine($"\nError: {ex.Message}");
                    Console.ResetColor();
                }
            }

            Console.WriteLine("\nPresione una tecla para volver al menú...");
            Console.ReadKey();
        }

        static void MenuEmitirLiquidacion()
        {
            Console.Clear();
            Console.WriteLine("--- EMITIR NUEVA LIQUIDACIÓN MENSUAL ---");
            
            Console.Write("Ingrese el Número de Cuenta: ");
            string numCuentaInput = Console.ReadLine();
            int numCuenta = Convert.ToInt32(numCuentaInput);

            Console.Write("Ingrese el Período (YYYY-MM): ");
            string periodo = Console.ReadLine();

            Console.Write("Ingrese la Fecha de Vencimiento (YYYY-MM-DD): ");
            string vencimiento = Console.ReadLine();

            Console.Write("Ingrese el Monto Total a Pagar: ");
            string totalInput = Console.ReadLine();
            double total = Convert.ToDouble(totalInput);

            Console.Write("Ingrese el Monto del Pago Mínimo: ");
            string minimoInput = Console.ReadLine();
            double minimo = Convert.ToDouble(minimoInput);

            using (MySqlConnection conexion = new MySqlConnection(connectionString))
            {
                try
                {
                    conexion.Open();

                    //verificar si la tarjeta existe
                    bool existeTarjeta = false;
                    string consultaChq = "SELECT 1 FROM tarjetas WHERE num_cuenta = @num_cuenta LIMIT 1";
                    using (MySqlCommand chqConsulta = new MySqlCommand(consultaChq, conexion))
                    {
                        chqConsulta.Parameters.AddWithValue("@num_cuenta", numCuenta);
                        using (MySqlDataReader lector = chqConsulta.ExecuteReader())
                        {
                            if (lector.HasRows)
                            {
                                existeTarjeta = true;
                            }
                        }
                    }
                    
                    if (!existeTarjeta)
                    {
                        Console.ForegroundColor = ConsoleColor.Red;
                        Console.WriteLine("\nError: La cuenta ingresada no existe.");
                        Console.ResetColor();
                    }
                    else
                    {
                        // insertar liquidación
                        string consultaLiquidacion = "INSERT INTO liquidaciones (num_cuenta, periodo, fecha_vencimiento, total_a_pagar, pago_minimo) " +
                                          "VALUES (@num_cuenta, @periodo, @vencimiento, @total, @minimo)";
                        using (MySqlCommand comando = new MySqlCommand(consultaLiquidacion, conexion))
                        {
                            comando.Parameters.AddWithValue("@num_cuenta", numCuenta);
                            comando.Parameters.AddWithValue("@periodo", periodo);
                            comando.Parameters.AddWithValue("@vencimiento", vencimiento);
                            comando.Parameters.AddWithValue("@total", total);
                            comando.Parameters.AddWithValue("@minimo", minimo);

                            int filas = comando.ExecuteNonQuery();

                            //actualizar saldo de la tarjeta
                            string consultaActualizarSaldo = "UPDATE tarjetas SET saldo = saldo + @total WHERE num_cuenta = @num_cuenta";
                            using (MySqlCommand comandoActualizarSaldo = new MySqlCommand(consultaActualizarSaldo, conexion))
                            {
                                comandoActualizarSaldo.Parameters.AddWithValue("@total", total);
                                comandoActualizarSaldo.Parameters.AddWithValue("@num_cuenta", numCuenta);
                                comandoActualizarSaldo.ExecuteNonQuery();
                            }

                            if (filas > 0)
                            {
                                Console.ForegroundColor = ConsoleColor.Green;
                                Console.WriteLine("\n¡Liquidación emitida correctamente!");
                                Console.ResetColor();
                            }
                        }
                    }
                }
                catch (Exception ex)
                {
                    Console.ForegroundColor = ConsoleColor.Red;
                    Console.WriteLine($"\nError: {ex.Message}");
                    Console.ResetColor();
                }
            }

            Console.WriteLine("\nPresione una tecla para volver al menú...");
            Console.ReadKey();
        }

        static void MenuListarTarjetas()
        {
            Console.Clear();
            Console.WriteLine("--- LISTADO GENERAL DE TARJETAS ---");
            Console.WriteLine("{0,-12} {1,-18} {2,-20} {3,-15}", "Nro Cuenta", "Nro Tarjeta", "Banco Emisor", "DNI Titular");
            Console.WriteLine("----------------------------------------------------------------------");

            ObtenerYMostrarTarjetas();

            Console.WriteLine("\nPresione una tecla para volver al menú...");
            Console.ReadKey();
        }

        static void MenuVerDetalleTarjeta()
        {
            Console.Clear();
            Console.WriteLine("--- DETALLE DE TARJETA Y CLIENTE ---");
            Console.Write("Ingrese el Número de Cuenta a consultar: ");
            int numCuenta = Convert.ToInt32(Console.ReadLine());

            MostrarDetalleCompleto(numCuenta);

            Console.WriteLine("\nPresione una tecla para volver al menú...");
            Console.ReadKey();
        }

        static void MenuEliminarTarjeta()
        {
            Console.Clear();
            Console.WriteLine("--- ELIMINAR TARJETA DEL SISTEMA ---");
            Console.Write("Ingrese el Número de Cuenta de la tarjeta a dar de baja: ");
            int numCuenta = Convert.ToInt32(Console.ReadLine());

            Console.ForegroundColor = ConsoleColor.Red;
            Console.WriteLine("\n ADVERTENCIA: Se eliminará la tarjeta, sus liquidaciones y los datos de acceso web vinculados.");
            Console.ResetColor();
            Console.Write("¿Está seguro de continuar? (S/N): ");
            
            if (Console.ReadLine().ToUpper() == "S")
            {
                bool exito = DarDeBajaTarjeta(numCuenta);

                if (exito)
                {
                    Console.ForegroundColor = ConsoleColor.Green;
                    Console.WriteLine("\nTarjeta y datos vinculados eliminados correctamente.");
                    Console.ResetColor();
                }
                else
                {
                    Console.ForegroundColor = ConsoleColor.Red;
                    Console.WriteLine("\nError al intentar eliminar la tarjeta. Verifique el número de cuenta.");
                    Console.ResetColor();
                }
            }
            else
            {
                Console.WriteLine("\nOperación cancelada.");
            }

            Console.WriteLine("\nPresione una tecla para volver al menú...");
            Console.ReadKey();
        }

        

        static void ObtenerYMostrarTarjetas()
        {
            using (MySqlConnection conexion = new MySqlConnection(connectionString))
            {
                try
                {
                    conexion.Open();
                    string consulta = "SELECT num_cuenta, numero_tarjeta, banco_emisor, dni_titular FROM tarjetas";
                    using (MySqlCommand comando = new MySqlCommand(consulta, conexion))
                    {
                        using (MySqlDataReader lector = comando.ExecuteReader())
                        {
                            while (lector.Read())
                            {
                                Console.WriteLine("{0,-12} {1,-18} {2,-20} {3,-15}", 
                                    lector["num_cuenta"].ToString(), 
                                    lector["numero_tarjeta"].ToString(), 
                                    lector["banco_emisor"].ToString(), 
                                    lector["dni_titular"].ToString());
                            }
                        }
                    }
                }
                catch (Exception ex)
                {
                    Console.ForegroundColor = ConsoleColor.Red;
                    Console.WriteLine($"Error: {ex.Message}");
                    Console.ResetColor();
                }
            }
        }

        static void MostrarDetalleCompleto(int cuenta)
        {
            using (MySqlConnection conexion = new MySqlConnection(connectionString))
            {
                try
                {
                    conexion.Open();
                    string consulta = "SELECT t.num_cuenta, t.numero_tarjeta, t.banco_emisor, t.estado, t.saldo, t.dni_titular, " +
                                   "u.tipo_doc, u.nombre, u.apellido, u.fecha_nacimiento, u.email, u.usuario " +
                                   "FROM tarjetas t " +
                                   "INNER JOIN usuarios u ON t.dni_titular = u.documento " +
                                   "WHERE t.num_cuenta = @cuenta";
                    using (MySqlCommand comando = new MySqlCommand(consulta, conexion))
                    {
                        comando.Parameters.AddWithValue("@cuenta", cuenta);
                        using (MySqlDataReader lector = comando.ExecuteReader())
                        {
                            if (lector.Read())
                            {
                                Console.ForegroundColor = ConsoleColor.Cyan;
                                Console.WriteLine("\n--------------------------------------------------");
                                Console.WriteLine($"Nro Cuenta:      {lector["num_cuenta"]}");
                                Console.WriteLine($"Nro Tarjeta:     {lector["numero_tarjeta"]}");
                                Console.WriteLine($"Banco Emisor:    {lector["banco_emisor"]}");
                                Console.WriteLine($"Estado:          {lector["estado"]}");
                                Console.WriteLine($"Saldo Actual:    $ {lector["saldo"]}");
                                Console.WriteLine("---------------- Titular -------------------------");
                                Console.WriteLine($"Documento:       {lector["dni_titular"]} ({lector["tipo_doc"]})");
                                Console.WriteLine($"Nombre Completo: {lector["nombre"]} {lector["apellido"]}");
                                Console.WriteLine($"Nacimiento:      {lector["fecha_nacimiento"]}");
                                Console.WriteLine($"Email:           {lector["email"]}");
                                Console.WriteLine($"Usuario Web:     {lector["usuario"]}");
                                Console.WriteLine("--------------------------------------------------");
                                Console.ResetColor();
                            }
                            else
                            {
                                Console.ForegroundColor = ConsoleColor.Yellow;
                                Console.WriteLine($"\nNo se encontró ninguna tarjeta con cuenta: {cuenta}");
                                Console.ResetColor();
                            }
                        }
                    }
                }
                catch (Exception ex)
                {
                    Console.ForegroundColor = ConsoleColor.Red;
                    Console.WriteLine($"Error: {ex.Message}");
                    Console.ResetColor();
                }
            }
        }

        static bool DarDeBajaTarjeta(int cuenta)
        {
            using (MySqlConnection conexion = new MySqlConnection(connectionString))
            {
                try
                {
                    conexion.Open();
                    string consultaObtenerDni = "SELECT dni_titular FROM tarjetas WHERE num_cuenta = @cuenta";
                    string dni = "";
                    using (MySqlCommand comandoDni = new MySqlCommand(consultaObtenerDni, conexion))
                    {
                        comandoDni.Parameters.AddWithValue("@cuenta", cuenta);
                        object result = comandoDni.ExecuteScalar();
                        if (result != null)
                        {
                            dni = result.ToString();
                        }
                    }

                    if (string.IsNullOrEmpty(dni))
                    {
                        return false;
                    }

                    string consultaEliminar = "DELETE FROM usuarios WHERE documento = @dni";
                    using (MySqlCommand comandoEliminar = new MySqlCommand(consultaEliminar, conexion))
                    {
                        comandoEliminar.Parameters.AddWithValue("@dni", dni);
                        int rows = comandoEliminar.ExecuteNonQuery();
                        return rows > 0;
                    }
                }
                catch (Exception ex)
                {
                    Console.ForegroundColor = ConsoleColor.Red;
                    Console.WriteLine($"Error: {ex.Message}");
                    Console.ResetColor();
                    return false;
                }
            }
        }
    }
}