export enum API_Methods {
  GET = 'GET',
  POST = 'POST',
  DELETE = 'DELETE',
  PUT = 'PUT',
  PATCH = 'PATCH',
}

export interface ApiResponse<DataType> {
  data: DataType;
  message: string;
  success: boolean;
}

export interface ErrorResponse {
  data: {
    statusCode: number;
    message: string;
    errors: {
      [key: string]: string[];
    };
  };
}

export interface PaginatedData<T> {
  data: T[];
  pagination: {
    total: number;
    count: number;
    perPage: number;
    currentPage: number;
    lastPage: number;
    from: number;
    to: number;
    firstPageUrl: string;
    nextPageUrl: string | null;
    prevPageUrl: string | null;
    lastPageUrl: string;
  };
}

export interface IPaginationParams {
  page?: number;
  per_page?: number;
}
