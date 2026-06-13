import { useMemo } from "react";
import { useAuth } from "../context/AuthContext";

export default function usePermission() {
  const { user } = useAuth();

  const permissions = useMemo(
    () => user?.permissions ?? [],
    [user]
  );

  const can = (permission) => {
    return permissions.includes(permission);
  };

  const canAny = (permissionList = []) => {
    return permissionList.some(
      (permission) =>
        permissions.includes(permission)
    );
  };

  const canAll = (permissionList = []) => {
    return permissionList.every(
      (permission) =>
        permissions.includes(permission)
    );
  };

  return {
    permissions,
    can,
    canAny,
    canAll,
  };
}