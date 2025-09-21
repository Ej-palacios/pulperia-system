<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'dashboard.view',
            
            // Clientes
            'clientes.view',
            'clientes.create',
            'clientes.edit',
            'clientes.delete',
            
            // Proveedores
            'proveedores.view',
            'proveedores.create',
            'proveedores.edit',
            'proveedores.delete',
            
            // Productos
            'productos.view',
            'productos.create',
            'productos.edit',
            'productos.delete',
            'productos.stock',
            
            // Categorías
            'categorias.view',
            'categorias.create',
            'categorias.edit',
            'categorias.delete',
            
            // Ventas
            'ventas.view',
            'ventas.create',
            'ventas.edit',
            'ventas.delete',
            'ventas.pos',
            'ventas.factura',
            
            // Compras
            'compras.view',
            'compras.create',
            'compras.edit',
            'compras.delete',
            
            // Créditos
            'creditos.view',
            'creditos.create',
            'creditos.edit',
            'creditos.delete',
            'creditos.cobrar',
            
            // Abonos
            'abonos.view',
            'abonos.create',
            'abonos.edit',
            'abonos.delete',
            
            // Inventario
            'inventario.view',
            'inventario.movimientos',
            'inventario.ajustes',
            
            // Gastos
            'gastos.view',
            'gastos.create',
            'gastos.edit',
            'gastos.delete',
            
            // Arqueo de Caja
            'arqueo.view',
            'arqueo.create',
            'arqueo.reportes',
            
            // Reportes
            'reportes.view',
            'reportes.ventas',
            'reportes.inventario',
            'reportes.financieros',
            'reportes.exportar',
            
            // Finanzas
            'finanzas.view',
            'finanzas.balance',
            'finanzas.flujo',
            
            // Usuarios
            'usuarios.view',
            'usuarios.create',
            'usuarios.edit',
            'usuarios.delete',
            'usuarios.roles',
            
            // Configuración
            'configuracion.view',
            'configuracion.edit',
            'configuracion.backup',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $roles = [
            'dueño' => [
                'dashboard.view',
                'clientes.view', 'clientes.create', 'clientes.edit', 'clientes.delete',
                'proveedores.view', 'proveedores.create', 'proveedores.edit', 'proveedores.delete',
                'productos.view', 'productos.create', 'productos.edit', 'productos.delete', 'productos.stock',
                'categorias.view', 'categorias.create', 'categorias.edit', 'categorias.delete',
                'ventas.view', 'ventas.create', 'ventas.edit', 'ventas.delete', 'ventas.pos', 'ventas.factura',
                'compras.view', 'compras.create', 'compras.edit', 'compras.delete',
                'creditos.view', 'creditos.create', 'creditos.edit', 'creditos.delete', 'creditos.cobrar',
                'abonos.view', 'abonos.create', 'abonos.edit', 'abonos.delete',
                'inventario.view', 'inventario.movimientos', 'inventario.ajustes',
                'gastos.view', 'gastos.create', 'gastos.edit', 'gastos.delete',
                'arqueo.view', 'arqueo.create', 'arqueo.reportes',
                'reportes.view', 'reportes.ventas', 'reportes.inventario', 'reportes.financieros', 'reportes.exportar',
                'finanzas.view', 'finanzas.balance', 'finanzas.flujo',
                'usuarios.view', 'usuarios.create', 'usuarios.edit', 'usuarios.delete', 'usuarios.roles',
                'configuracion.view', 'configuracion.edit', 'configuracion.backup',
            ],
            'administrador' => [
                'dashboard.view',
                'clientes.view', 'clientes.create', 'clientes.edit', 'clientes.delete',
                'proveedores.view', 'proveedores.create', 'proveedores.edit', 'proveedores.delete',
                'productos.view', 'productos.create', 'productos.edit', 'productos.delete', 'productos.stock',
                'categorias.view', 'categorias.create', 'categorias.edit', 'categorias.delete',
                'ventas.view', 'ventas.create', 'ventas.edit', 'ventas.delete', 'ventas.pos', 'ventas.factura',
                'compras.view', 'compras.create', 'compras.edit', 'compras.delete',
                'creditos.view', 'creditos.create', 'creditos.edit', 'creditos.delete', 'creditos.cobrar',
                'abonos.view', 'abonos.create', 'abonos.edit', 'abonos.delete',
                'inventario.view', 'inventario.movimientos', 'inventario.ajustes',
                'gastos.view', 'gastos.create', 'gastos.edit', 'gastos.delete',
                'arqueo.view', 'arqueo.create', 'arqueo.reportes',
                'reportes.view', 'reportes.ventas', 'reportes.inventario', 'reportes.financieros', 'reportes.exportar',
                'finanzas.view', 'finanzas.balance', 'finanzas.flujo',
                'usuarios.view', 'usuarios.create', 'usuarios.edit', 'usuarios.delete', 'usuarios.roles',
                'configuracion.view', 'configuracion.edit',
            ],
            'vendedor' => [
                'dashboard.view',
                'clientes.view', 'clientes.create', 'clientes.edit',
                'productos.view',
                'ventas.view', 'ventas.create', 'ventas.pos', 'ventas.factura',
                'creditos.view', 'creditos.cobrar',
                'abonos.view', 'abonos.create',
                'reportes.view', 'reportes.ventas',
            ],
            'cajero' => [
                'dashboard.view',
                'clientes.view', 'clientes.create', 'clientes.edit',
                'productos.view',
                'ventas.view', 'ventas.create', 'ventas.pos', 'ventas.factura',
                'abonos.view', 'abonos.create',
                'arqueo.view', 'arqueo.create',
            ],
            'inventario' => [
                'dashboard.view',
                'productos.view', 'productos.create', 'productos.edit', 'productos.stock',
                'categorias.view', 'categorias.create', 'categorias.edit',
                'compras.view', 'compras.create', 'compras.edit',
                'inventario.view', 'inventario.movimientos', 'inventario.ajustes',
                'reportes.view', 'reportes.inventario',
            ],
            'usuario' => [
                'dashboard.view',
                'clientes.view',
                'productos.view',
                'ventas.view',
                'reportes.view',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }
    }
}
