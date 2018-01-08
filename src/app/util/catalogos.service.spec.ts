import { TestBed, inject } from '@angular/core/testing';

import { CatalogosService } from './catalogos.service';

describe('CatalogosService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [CatalogosService]
    });
  });

  it('should be created', inject([CatalogosService], (service: CatalogosService) => {
    expect(service).toBeTruthy();
  }));
});
